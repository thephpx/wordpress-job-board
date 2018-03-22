<?php 

namespace App;

class Plugin{
  
  private static $instance;
	private $wpdb;
  
  private function __construct()
  {
    
  }
	
	public function setDB(&$wpdb)
	{
		$this->wpdb = $wpdb;
	}
  
  public static function getInstance()
  {
    if(static::$instance === null)
    {
      static::$instance = new static();
    }
    
    return static::$instance;
  }

	public function initialize()
	{
		add_action('init', array($this, 'bootstrap'));
	}
  
  public function bootstrap()
  {
		$this->add_post_capability();
    $this->create_post_types();
		$this->register_taxonomy_jobtype();
		$this->register_taxonomy_applicationstateus();
		add_shortcode( 'job_list', array( $this, 'shortcode_job_list' ) );
		add_shortcode( 'job_detail', array( $this, 'shortcode_job_detail' ) );
  }
	
	public function activate(){
		flush_rewrite_rules();
	}
	
	public function deactivate(){
	}
	
	public function shortcode_job_list()
	{	
		$jobs = $this->query_jobs($_GET);	
		//print '<pre>'; print_r($jobs->posts);exit;
		return $this->render('job_list', array('jobs'=>$jobs->posts));
	}
	
	public function shortcode_job_detail()
	{
		$job = $this->query_job($_GET);	

		return $this->render('job_detail', array('job'=>$job->post));		
	}
	
	private function render($page="",$data)
	{
		if(empty($page)) return '';
		
		ob_start();
		include_once( APPL_PLUGINDIR . '/templates/' . $page.'.php' );
		$template_html = ob_get_contents();
		ob_end_clean();
		return $template_html;
	}
	
	private function query_job($arguments=array())
	{
		$args = array();
		$args['id'] = $arguments['id'];
		$args['post_type'] = 'job';
		$args['post_status'] = 'publish';

		return new \WP_Query( $args );
	}
	
	private function query_jobs($arguments=array())
	{
		$args = array();
		$args['post_type'] = 'job';
		$args['post_status'] = 'publish';
		if(isset($arguments['filter_type'])){
		$args['tax_query'] = array(
			array(
				"taxonomy" => "job_type",
				"field" => "name",
				"terms" => array(esc_sql($arguments['filter_type']))
			)
		);
		}
		// = 'Full-Time';

		return new \WP_Query( $args );
	}
	
	private function filter_get_job_types($id=0)
	{
		$terms = get_the_terms($id,'job_type');
		//print_r($terms);
		if(!is_wp_error($terms)){
			$job_types = array_map(function($term){
				return $term->name;

			}, $terms);
			return implode(", ",$job_types);
		}else{
			return 'N/A';
		}
	}
	
	private function add_post_capability()
	{
		$admin_role = get_role('administrator');
		
		if($admin_role)
		{
			$admin_role->add_cap('read_jobs');
			$admin_role->add_cap('edit_jobs');
			$admin_role->add_cap('edit_job');
			$admin_role->add_cap('delete_jobs');
			$admin_role->add_cap('delete_job');
			$admin_role->add_cap('publish_jobs');
			
			$admin_role->add_cap('read_applications');
			$admin_role->add_cap('edit_applications');
			$admin_role->add_cap('edit_application');
			$admin_role->add_cap('delete_applications');
			$admin_role->add_cap('delete_application');
			$admin_role->add_cap('publish_applications');
		}
	}
	
	public function bind_meta_box()
	{		
		add_meta_box('job_meta','Job Fields',array($this, 'add_job_meta_box'), 'job','normal','default');
		add_meta_box('application_meta','Application Fields',array($this, 'add_application_meta_box'), 'application','normal','default');
	}
	
	public function add_job_meta_box()
	{
		wp_nonce_field( basename( APPL_PLUGINFILE ), 'application_fields' );
		
		$job_salary = get_post_meta( get_the_ID(), 'job_salary', true );
		$job_deadline = get_post_meta( get_the_ID(), 'job_deadline', true );
		$job_attachment = get_post_meta( get_the_ID(), 'job_attachment', true );
		$job_logo = get_post_meta( get_the_ID(), 'job_logo', true );
		
		print '<p><label>Salary</label><input type="text" name="job_salary" value="' . esc_attr( $job_salary )  . '" class="widefat"></p>';
		print '<p><label>Deadline</label><input type="date" id="datepicker" name="job_deadline" value="' . esc_attr( $job_deadline )  . '" class="widefat"></p>';
		print '<p><label>Attachment</label><input type="file" name="job_attachment" value="' . esc_attr( $job_attachment )  . '" class="widefat"></p>';
		print '<p><label>Company Logo</label><input type="file" name="job_logo" value="' . esc_attr( $job_logo )  . '" class="widefat"></p>';
		
	}
	
	public function add_application_meta_box()
	{
		wp_nonce_field( basename( APPL_PLUGINFILE ), 'application_fields' );
		
		$job_id = get_post_meta( get_the_ID(), 'job_id', true );
		$application_salary_desired = get_post_meta( get_the_ID(), 'application_salary_desired', true );
		$application_expected_join_date = get_post_meta( get_the_ID(), 'application_expected_join_date', true );
		$application_attachment = get_post_meta( get_the_ID(), 'application_attachment', true );
		
		print '<p><label>Job Id:</label><input type="hidden" name="job_id" value="' . esc_attr( $job_id )  . '" class="widefat"></p>';
		print '<p><label>Join Date:</label><input type="hidden" name="application_expected_join_date" value="' . esc_attr( $application_expected_join_date )  . '" class="widefat"></p>';
		print '<p><label>Salary Expectation:</label><input type="hidden" name="application_salary_desired" value="' . esc_attr( $application_salary_desired )  . '" class="widefat"></p>';
		print '<p><label>Attachment:</label><input type="hidden" name="application_attachment" value="' . esc_attr( $application_attachment )  . '" class="widefat"></p>';
		
	}

	private function register_taxonomy_jobtype()
	{
		$labels = array(
			'name' => __( 'Job Type' ),
			'singular_name' => __( 'Job Type' ),
			'add_new' => __( 'Add New' ),
			'add_new_item' => __( 'Add New Job Type' ),
			'edit_item' => __( 'Edit Job Type' ),
			'new_item' => __( 'New Job Type' ),
			'view_item' => __( 'View Job Type' ),
			'search_items' => __( 'Search Job Types' ),
			'not_found' => __( 'No categories found' ),
			'not_found_in_trash' => __( 'No categories found in Trash' ),
			'parent_item_colon' => __( 'Parent Job Type:' ),
			'menu_name' => __( 'Job Types' ),
		);

		$args = array(
			'labels' => $labels,
			'hierarchical' => true,
			'description' => 'Job Type',
			'public' => true
		);

		register_taxonomy( 'job_type', array('job') , $args);

	}
	
	private function register_taxonomy_applicationstateus()
	{
		$labels = array(
			'name' => __( 'Application Status' ),
			'singular_name' => __( 'Application Status' ),
			'add_new' => __( 'Add New' ),
			'add_new_item' => __( 'Add New Application Status' ),
			'edit_item' => __( 'Edit Application Status' ),
			'new_item' => __( 'New Application Status' ),
			'view_item' => __( 'View Application Status' ),
			'search_items' => __( 'Search Application Status' ),
			'not_found' => __( 'No applications found' ),
			'not_found_in_trash' => __( 'No applications found in Trash' ),
			'parent_item_colon' => __( 'Parent Application Status:' ),
			'menu_name' => __( 'Application Status' ),
		);

		$args = array(
			'labels' => $labels,
			'hierarchical' => true,
			'description' => 'Application Status',
			'public' => true
		);

		register_taxonomy( 'application_status', array('application') , $args);
		
	}
  
  private function create_post_types()
  {
    $labels = array(
			'name' => __( 'Jobs' ),
			'singular_name' => __( 'Job' ),
			'add_new' => __( 'Add New' ),
			'add_new_item' => __( 'Add New Job' ),
			'edit_item' => __( 'Edit Job' ),
			'new_item' => __( 'New Job' ),
			'view_item' => __( 'View Job' ),
			'search_items' => __( 'Search Jobs' ),
			'not_found' => __( 'No jobs found' ),
			'not_found_in_trash' => __( 'No jobs found in Trash' ),
			'parent_item_colon' => __( 'Parent Job:' ),
			'menu_name' => __( 'Jobs' ),
		);

		$args = array(
			'labels' => $labels,
			'map_meta_cap'=>false,
			'hierarchical' => false,
			'description' => 'Job',
			'supports' => array( 'title', 'editor', 'author'),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_icon' => 'dashicons-admin-page',
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type'=>'job',
			'register_meta_box_cb' => array($this, 'bind_meta_box')
		);
		
    register_post_type( 'job', $args );
		
    $labels = array(
			'name' => __( 'Applications' ),
			'singular_name' => __( 'Application' ),
			'add_new' => __( 'Add New' ),
			'add_new_item' => __( 'Add New Application' ),
			'edit_item' => __( 'Edit Application' ),
			'new_item' => __( 'New Application' ),
			'view_item' => __( 'View Application' ),
			'search_items' => __( 'Search Applications' ),
			'not_found' => __( 'No applications found' ),
			'not_found_in_trash' => __( 'No applications found in Trash' ),
			'parent_item_colon' => __( 'Parent Application:' ),
			'menu_name' => __( 'Applications' ),
		);

		$args = array(
			'labels' => $labels,
			'map_meta_cap'=>false,
			'hierarchical' => false,
			'description' => 'Application',
			'supports' => array( 'title', 'editor', 'author'),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_icon' => 'dashicons-admin-page',
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type'=>'application',
			'register_meta_box_cb' => array($this, 'bind_meta_box')
		);
		
    register_post_type( 'application', $args );
  }
  
}
<?php

use App\Plugin as Plugin;

class JobPlugin extends WP_UnitTestCase{
  protected $plugin_file;
  
  public function setUp() {
    $this->plugin_file = dirname( dirname( __FILE__ ) ) . '/wordpress-job-board.php';
    parent::setUp();
		
    $page = array();
		$page['post_type'] = 'page';
		$page['post_title'] = 'Job List';
		$page['post_content'] = '[job_list]';
		$page['post_name'] = 'job-list';
		$page['post_status'] = 'publish';
    
		$outcome = \wp_insert_post($page);
		
    $page = array();
		$page['post_type'] = 'page';
		$page['post_title'] = 'Job Detail';
		$page['post_content'] = '[job_detail]';
		$page['post_name'] = 'job-detail';
		$page['post_status'] = 'publish';
    
		$outcome = \wp_insert_post($page);
		
    $page = array();
		$page['post_type'] = 'page';
		$page['post_title'] = 'Job Post';
		$page['post_content'] = '[job_post]';
		$page['post_name'] = 'job-post';
		$page['post_status'] = 'publish';
    
		$outcome = \wp_insert_post($page);
  }
  
  function testJobRetrival()
  {
    $plugin = Plugin::getInstance();
    
    if($plugin instanceof App\Plugin)
    {
      $outcome = true;
    }else{
      $outcome = false;
    }
    
    $this->assertTrue($outcome);
  }
  
  function testPluginActive()
  {    
    $this->assertTrue(true);
  }
  
  function testJobListShortCodeExists()
  {
    $outcome = shortcode_exists('job_list');
    $this->assertTrue($outcome);
  }
  
  function testJobDetailShortCodeExists()
  {
    $outcome = shortcode_exists('job_detail');
    $this->assertTrue($outcome);
  }
  
  function testJobPostShortCodeExists()
  {
    $outcome = shortcode_exists('job_post');
    $this->assertTrue($outcome);
  }
  
  function testJobListPageBySlug()
  {		
    $test = get_page_by_title('Job List', OBJECT, 'page');
    
    $this->assertNotNull($test);
		$this->assertContains('Job List',serialize($test));
  }
  
  function testJobPageActive()
  {
    $test = get_page_by_title('Job Detail', OBJECT, 'page');
    
    $this->assertNotNull($test);
		$this->assertContains('Job Detail',serialize($test));
  }
  
  function testJobPageContents()
  {
    $test = get_page_by_title('Job Post', OBJECT, 'page');
    
    $this->assertNotNull($test);
		$this->assertContains('Job Post',serialize($test));   
  }
}
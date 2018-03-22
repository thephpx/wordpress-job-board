<?php 
namespace App;

use \WP_Post;

class Job{
  
  private $post;
  
  private $type;        // Required; Full-Time, Part-Time, Remote, Flex-Time
  private $title;       // Required;
  private $description; // Required;
  private $salary;      // Not-Required
  private $deadline;    // Required
  private $attachment;  // Not-Required
  private $logo;        // Not-Required
  
  public function __construct()
  {
  }
  
  public function __get($name)
  {
    if(isset($this->{$name}))
    {
      return $this->{$name};
    }else{
      return null;
    }
  }
  
  public function __set($name, $value)
  {
    $this->{$name} = $value;
  }
  
  public function get($id=null)
  {
    if($id != null){
      return $this->post = \WP_Post::get_instance($id);
    }else{
      return false;
    }
  }
  
  public function save()
  {
    
  }
  
  public function delete($id=null)
  {
    if($id > 0)
    {
      
    }else{
      return false;
    }
    
  }
  
  
}
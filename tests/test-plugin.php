<?php

use App\Plugin as Plugin;

class JobPlugin extends WP_UnitTestCase{
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
}
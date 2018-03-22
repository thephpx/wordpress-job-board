<?php

use App\Job as Job;

class JobTest extends WP_UnitTestCase{
  function testJobRetrival()
  {
    $job = new Job();
    $job->get(1);
    
    if($job instanceof App\Job)
    {
      $outcome = true;
    }else{
      $outcome = false;
    }
    
    $this->assertTrue($outcome);
  }
}
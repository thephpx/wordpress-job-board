<p>
  <ul>
    <li><a href="?">All Jobs</a></li>
    <li><a href="?filter_type=Full-Time">Full-Time</a></li>
    <li><a href="?filter_type=Part-Time">Part-Time</a></li>
    <li><a href="?filter_type=Remote">Remote</a></li>
  </ul>
</p> 
<table class="table">
  <thead>
    <tr>
      <th>Job Title</th>
      <th>Job Type</th>
      <th>Job Detail</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($data['jobs'] as $job){ ?>
    <tr>
      <td><?php print $job->post_title; ?></td>
      <td><?php print $this->filter_get_job_types($job->ID); ?></td>
      <td><a href="<?php print site_url('/job-detail/?id='.$job->ID); ?>">Details</a></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
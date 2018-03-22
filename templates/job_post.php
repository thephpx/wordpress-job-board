<form method="post" action="<?php print ( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
  <input type="hidden" name="action" value="job_post_action"/>
  <?php wp_nonce_field( basename( APPL_PLUGINFILE ), 'job_post_nonce' ); ?>
  <p>
    <label>Job Title</label>
    <input type="text" name="job_title"/>
  </p>
  <p>
    <label>Job Description</label>
    <textarea name="job_description"></textarea>
  </p>
  <p>
    <label>Type</label>
    <select name="job_type">
      <option value="full-time">Full-Time</option>
      <option value="part-time">Part-Time</option>
      <option value="remote">Remote</option>
      <option value="freelance">Freelance</option>
    </select>
  </p>
  <p>
    <label>Deadline</label>
    <input type="date" name="job_deadline"/>
  </p>
  <p>
    <label>Salary</label>
    <input type="text" name="job_salary"/>
  </p>
  <p>
    <label>Logo</label>
    <input type="file" name="job_logo"/>
  </p>
  <p>
    <label>Attachment</label>
    <input type="file" name="job_attachment"/>
  </p>
  <p>
    <button type="submit">
      Post Job
    </button>
  </p>
</form>
<?php

  $all_cache_tempfile = "all.cache.temp";
  $all_cache_file = "all.cache";
  $sync_cache_tempfile = "sync.cache.temp";
  $sync_cache_file = "sync.cache";
  $summary_cache_tempfile = "summary.cache.temp";
  $summary_cache_file = "summary.cache";
  $extended_cache_tempfile = "extended.cache.temp";
  $extended_cache_file = "extended.cache";
  
  if (file_exists ($all_cache_file)) if (file_exists ($all_cache_tempfile)) unlink($all_cache_tempfile);
  if (file_exists ($sync_cache_file)) if (file_exists ($sync_cache_tempfile)) unlink($sync_cache_tempfile);
  if (file_exists ($summary_cache_file)) if (file_exists ($summary_cache_tempfile)) unlink($summary_cache_tempfile);
  if (file_exists ($extended_cache_file)) if (file_exists ($extended_cache_tempfile)) unlink($extended_cache_tempfile);    
?>
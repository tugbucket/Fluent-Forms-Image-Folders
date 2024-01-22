<?php
/**
* Plugin Name: Fluent Forms - Image Folders
* Plugin URI: 
* Description: Creates folders in /uploads/fluentform, accessible by FTP,  named your_form_name and renames files <form id>-firstname_lastname-<index>-<extension>
* Version: 1.0
* Author: Alan Jackson
* Author URI: https://tugbucket.net
* License: GPL3
**/

add_action('fluentform/before_insert_submission', 'fluent_hijack_file_upload_tug29jsdw', 10, 3);
function fluent_hijack_file_upload_tug29jsdw($insertData, $data, $form)
{
	$form_title = strtolower(preg_replace('/\s+/', '_', $form->title));
	$uploads_dir = trailingslashit(wp_upload_dir()['basedir']).'fluentform/'.$form_title;
	if(!is_dir($uploads_dir)){
		wp_mkdir_p($uploads_dir);
	}
}

add_action('fluentform/submission_inserted', 'fluent_hijack_file_rename_tug29jsdw', 20, 3);
function fluent_hijack_file_rename_tug29jsdw($entryId, $formData, $form)
{
	if(is_array($formData['image-upload'])){
		$i = 1;
		foreach($formData['image-upload'] as $image){
			$imgurl = $image;
			$form_title = strtolower(preg_replace('/\s+/', '_', $form->title));
			$old = $imgurl;
			$ext = pathinfo($old, PATHINFO_EXTENSION);
			$name = pathinfo($old, PATHINFO_FILENAME);	
			$fname = strtolower(preg_replace('/\s+/', '_', $formData['names']['first_name']));
			$lname = strtolower(preg_replace('/\s+/', '_', $formData['names']['last_name']));
			$new = trailingslashit(wp_upload_dir()['basedir']).'fluentform/'.$form_title.'/'.$entryId.'-'.$fname.'-'.$lname.'-'.$i++.'.'.$ext;
			copy($old,$new);
		}
	}
}

add_action('fluentform/after_deleting_submissions', function ($submissionIds, $formId)
{
	$form = wpFluent()->table('fluentform_forms')->find($formId);
	$form_title = strtolower(preg_replace('/\s+/', '_', $form->title));
	$uploads_dir = trailingslashit(wp_upload_dir()['basedir']).'fluentform/'.$form_title;
	$files = glob($uploads_dir . '/*');
	if(!empty($files)){
		foreach ($files as $file) {
			foreach($submissionIds as $id){
				if (strstr($file, $id)) {
					unlink($file);
				}
			}
		}
	}
}, 10, 2);
?>
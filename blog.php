<?php 
require_once "/includes/header.php"; //Initialize

$Blog = new BlogPost();
$Blog->setPage((Input::get("page") != '') ? Input::get("page") : 1);
$Posts = '';

if(($i = Input::get("id")) != ''){
	$Blog->get(array('id'=>$i));
} elseif(($i = Input::get("tag")) != ''){
	$Blog->get(array('tag'=>$i));
} elseif(($i = Input::get("author")) != ''){
	$Blog->get(array('author'=>$i));
} else {
	$Blog->get();
}

if($Blog->error404){
	echo '404<br>';
}

if(!$Blog->singlePost){
	//Display Multiple
	foreach($Blog as $BlogPost){
		$Template = new Template('includes/templates/blog/SlotView.html');	
		$Template->arrayset(get_object_vars($BlogPost));
		$Posts .= $Template->parse();
	}
} else {
	//Lone Post display
	$Template = new Template('includes/templates/blog/ExpandedView.html');	
	$Template->arrayset(get_object_vars($Blog->data()[0]));
	$Posts .= $Template->parse();
	$Comments = new BlogComment($Blog->data()[0]->id);
	
	if(count($Comments) != 0){
		$Posts .= "<h3>Comments: </h3>";
		foreach($Comments as $Comment){
			$Template = new Template('includes/templates/blog/CommentSlot.html');	
			$Template->arrayset(get_object_vars($Comment));
			$Posts .= $Template->parse();
		}
	} else {
		$Posts .= 'No comments';
	}
}

echo $Posts;//DIsplay it all

require_once "/includes/footer.php"; //Initialize
?>
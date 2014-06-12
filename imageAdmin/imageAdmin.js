// imageAdmin.js

$(document).ready(function(){
	$(".folderId").change(function() {
		var folderId = $(".folderId").val();
		getPhotosThisFolder(folderId, 1);
	});
});

function getPhotosThisFolder(folderId, page) {
	var ajaxUrl = "index.php?fuseAction=getPhotos&folderId=" + folderId + "&page=" + page;
	$("#photos").load(ajaxUrl, loadPhotosCallback);
}

function loadPhotosCallback() {
	setPagination();
}

function setPagination() {
	$(".pagination a").click(function(event) {
		var srcElement = $(event.target);
		var page = srcElement.data("page");
		var folderId = srcElement.parent().data("folderid");
		getPhotosThisFolder(folderId, page);
	});
}


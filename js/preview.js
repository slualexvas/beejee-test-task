
	function preview()
	{
		var preview_div = document.getElementById('preview_div');
		var text_src = document.getElementById('text');
		var username_src = document.getElementById('username');
		var email_src = document.getElementById('email');
		
		preview_div.innerHTML = "<h2>Задача №... (не выполнена)</h2>";
		preview_div.innerHTML += "<div class='name'><b>Создал:</b> " + username_src.value;
		preview_div.innerHTML += "<a href='mailto:" + email_src.value + "'>" + email_src.value + "</a></div>	";
		preview_div.innerHTML += "<div class='text'>" + text_src.value + "</div>";
		preview_div.className = "task";
	}

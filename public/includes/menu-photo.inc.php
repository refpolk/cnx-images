<nav class="navbar navbar-inverse navbar-static-top">
  <div class="container">
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav navbar-left">
          <li><a href="search-image.php">Images</a></li>
          <li class="active"><a href="search-photo.php">Photos</a></li>
      </ul>
	  <ul class="nav navbar-nav navbar-right">
		<li <?php if (strtok($_SERVER['REQUEST_URI'],'?') == "/edit-photo.php") echo "class=\"active\""; ?>><a href="edit-photo.php">Add</a></li>
		<li <?php if (strtok($_SERVER['REQUEST_URI'],'?') == "/search-photo.php") echo "class=\"active\""; ?>><a href="search-photo.php">Search</a></li>
		<li <?php if (strtok($_SERVER['REQUEST_URI'],'?') == "/browse-photo.php") echo "class=\"active\""; ?>><a href="browse-photo.php">Browse</a></li>
		<li <?php if (strtok($_SERVER['REQUEST_URI'],'?') == "/import-photo.php") echo "class=\"active\""; ?>><a href="import-photo.php">Import</a></li>
	  </ul>		
    </div>
  </div>
</nav>
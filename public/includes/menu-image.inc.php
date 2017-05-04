<nav class="navbar navbar-inverse navbar-static-top">
  <div class="container">
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav navbar-left">
          <li class="active"><a href="search-image.php">Images</a></li>
          <li><a href="search-photo.php">Photos</a></li>
      </ul>
	  <ul class="nav navbar-nav navbar-right">
		<li <?php if (strtok($_SERVER['REQUEST_URI'],'?') == "/edit-image.php") echo "class=\"active\""; ?>><a href="edit-image.php">Add</a></li>
		<li <?php if (strtok($_SERVER['REQUEST_URI'],'?') == "/search-image.php") echo "class=\"active\""; ?>><a href="search-image.php">Search</a></li>
		<li <?php if (strtok($_SERVER['REQUEST_URI'],'?') == "/browse-image.php") echo "class=\"active\""; ?>><a href="browse-image.php">Browse</a></li>
		<li <?php if (strtok($_SERVER['REQUEST_URI'],'?') == "/import-image.php") echo "class=\"active\""; ?>><a href="import-image.php">Import</a></li>
	  </ul>		
    </div>
  </div>
</nav>
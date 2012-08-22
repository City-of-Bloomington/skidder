<nav>
	<a href="<?php echo BASE_URI; ?>">Home</a>
	<?php
		if (isset($_SESSION['USER'])) {
			echo "
			<a href=\"".BASE_URI."/subscriptions\">Subscriptions</a>
			<a href=\"".BASE_URI."/login/logout\">Logout</a>
			";
		}
		else {
			echo "
			<a href=\"".BASE_URI."/login?return_url=$_SERVER[REQUEST_URI]\">
				Login
			</a>
			";
		}
	?>
</nav>

<nav id="sidebar">
    <div class="sidebar-header">
        <h3>Zafire Distributors</h3>
    </div>

    <ul class="list-unstyled components">
        <p>
            <?php displayCurrentUserFull(); ?>
        </p>
        <li>
            <a href="home.php">Home</a>
        </li>
        <li class="">
            <a href="#" data-toggle="" aria-expanded="false" class="">Requests</a>
            <ul class="list-unstyled" id="homeSubmenu">
                <li>
                    <a href="type.php">Create</a>
                </li>
                <li>
                    <a href="my.php">My Requests</a>
                </li>
                <!-- <li>
                    <a href="pending.php">Pending</a>
                </li>
                <li>
                    <a href="ongoing.php">Ongoing</a>
                </li>
                <li>
                    <a href="completed.php">Completed</a>
                </li>
                <li>
                    <a href="rejected.php">Rejected</a>
                </li>
                <li>
                    <a href="overview.php">Overview</a>
                </li> -->
            </ul>
        </li>
        <!-- <li>
            <a href="users.php">Users</a>
        </li> -->
        <li>
            <a href="../login.php?logout='1'">Logout</a>
        </li>
    </ul>
</nav>

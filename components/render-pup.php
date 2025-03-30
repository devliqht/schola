<?php
function render_pup() {
?>
<div class="pup-wrapper" style="position: fixed; width: 280px;">
        <h3 class="text-xl text-white inter-700 pt-4 pb-2">Your Groups</h3>
            <div class="flex flex-col gap-4">
                <?php 
                    $conn = establish_connection();
                    $group_query = "SELECT mg.*, u.username as creator_username
                                    FROM group_members gm
                                    JOIN member_groups mg ON gm.group_id = mg.id
                                    JOIN users u ON mg.creator_id = u.id
                                    WHERE gm.user_id = ?
                    ";
                    $group_stmt = $conn->prepare($group_query);
                    $group_stmt->bind_param("i", $_SESSION['id']);
                    $group_stmt->execute();
                    $groups = $group_stmt->get_result();
                    $defaultProfilePicture = "../uploads/profile_pictures/default.svg";
                ?>
                <?php while ($group = $groups->fetch_assoc()): ?>
                    <?php $groupProfilePicture = !empty($group['group_picture']) ? "../uploads/group_pictures/" . $group['group_picture'] : $defaultProfilePicture; ?>
                    <div class="group-card">
                    <a href="group.php?id=<?php echo $group['id']; ?>" class="group-link"></a>
                        <img class="header-account-picture" src="<?php echo $groupProfilePicture ?>" />
                        <div class="flex flex-col">
                            <h2 class="text-base gradient-text inter-600"><?= $group['name']; ?></h2>
                            <p class="group-card-desc text-sm inter-300 text-white"><?= $group['description']; ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <h3 class="text-xl text-white inter-700 pt-6 pb-2">Calendar Events</h3>
            <div class="flex flex-col gap-4">
            <?php   
                $events_query = "SELECT e.* FROM events e";
                $events_stmt = $conn->prepare($events_query);
                $events_stmt->execute();
                $events = $events_stmt->get_result();
            ?>
            <?php while ($event = $events->fetch_assoc()): 
                    $event_date = DateTime::createFromFormat('Y-m-d', $event['event_date']);
                    $day = $event_date->format('d'); // Extract day
                    $month = $event_date->format('M'); // Extract abbreviated month
                ?>
                
                <div class="group-card">
                <a href="event.php?id=<?php echo $event['id']; ?>" class="group-link"></a>
                    <div class="flex flex-row gap-4">
                        <div class="date-box flex flex-col items-center justify-center">
                            <span class="text-lg uppercase inter-600 text-gray-400 gradient-text"><?= $month; ?></span>
                            <span class="text-4xl inter-700"><?= $day; ?></span>
                        </div>
                        <div class="flex flex-col">
                            <h2 class="text-base gradient-text inter-600 pup-event-title"><?= $event['title']; ?></h2>
                            <p class="group-card-desc text-sm inter-300 text-white"><?= $event['description']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>

            <h3 class="text-xl text-white inter-700 pt-6 pb-2">Active Users</h3>
            <div class="flex flex-col gap-4">
                <?php
                    $users_query = "SELECT u.id, u.username, u.full_name, u.profile_picture, u.is_active, u.last_active
                    FROM users u
                    ORDER BY u.last_active DESC
                    LIMIT 7
                    ";
                    $users_stmt = $conn->prepare($users_query);
                    $users_stmt->execute();
                    $users = $users_stmt->get_result();

                ?>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <?php 
                    $profilePicture = !empty($user['profile_picture']) ? "../uploads/profile_pictures/" . $user['profile_picture'] : $defaultProfilePicture;
                    $status = $user['is_active'] ? "Active now" : "Last active " . formatRelativeTime($user['last_active']);
                    ?>
                    <div class="flex flex-row gap-4 align-center">
                        <img class="header-account-picture" src="<?php echo $profilePicture; ?>" alt="Profile Picture"/>
                        <div class="flex flex-col">
                            <h2 class="text-base gradient-text inter-600"><?php echo htmlspecialchars($user['full_name']); ?></h2>
                            <a href="profile.php?id=<?php echo $user['id']; ?>" class="text-sm text-white inter-400 decoration-none hover-underline">@<?php echo htmlspecialchars($user['username']); ?></a>
                            <div class="user-status pt-1">
                                <span class="status-dot <?php echo $user['is_active'] ? 'active' : 'inactive'; ?>"></span>
                                <span class="status-text post-date" data-timestamp="<?php echo $user['last_active']; ?>">
                                    <?php echo $user['is_active'] ? 'Active now' : 'Last active ' . formatRelativeTime($user['last_active']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
<?php } ?>
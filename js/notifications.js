document.addEventListener('DOMContentLoaded', function() {
    const notificationBell = document.getElementById('notification-bell');
    const notificationModal = document.getElementById('notification-modal');
    const closeNotifications = document.getElementById('close-notifications');
    const notificationList = document.getElementById('notification-list');
    const notificationCount = document.getElementById('notification-count');

    const backdrop = document.createElement('div');
    backdrop.className = 'notification-backdrop';
    document.body.appendChild(backdrop);

    const notifyButton = document.getElementById('notify-button');
    
    loadNotificationCount();
    
    if (notificationBell) {
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            openNotificationModal();
        });
    }

    if (closeNotifications) {
        closeNotifications.addEventListener('click', function() {
            closeNotificationModal();
        });
    }

    backdrop.addEventListener('click', function() {
        closeNotificationModal();
    });

    if (notifyButton) {
        notifyButton.addEventListener('click', toggleNotification);
        checkNotificationStatus();
    }

    function openNotificationModal() {
        notificationModal.classList.add('active');
        backdrop.classList.add('active');
        loadNotifications();
    }

    function closeNotificationModal() {
        notificationModal.classList.remove('active');
        backdrop.classList.remove('active');
    }

    function loadNotificationCount() {
        fetch('../api/get-notification-count.php')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    notificationCount.textContent = data.count;
                    notificationCount.classList.add('active');
                } else {
                    notificationCount.classList.remove('active');
                }
            });
    }

    function loadNotifications() {
        fetch('../api/get-notifications.php')
            .then(response => response.json())
            .then(data => {
                notificationList.innerHTML = '';
                data.notifications.forEach(notification => {
                    const notificationItem = createNotificationElement(notification);
                    notificationList.appendChild(notificationItem);
                });
            });
    }

    function createNotificationElement(notification) {
        const notificationItem = document.createElement('div');
        notificationItem.className = 'notification-item' + (notification.read_status === 0 ? ' unread' : '');
        
        const notificationContent = document.createElement('div');
        notificationContent.className = 'notification-content';
        
        const avatarImg = document.createElement('img');
        avatarImg.className = 'notification-avatar';
        avatarImg.src = notification.profile_picture ? `../uploads/profile_pictures/${notification.profile_picture}` : '../uploads/profile_pictures/default.svg';
        avatarImg.alt = 'User Avatar';
        
        const notificationText = document.createElement('div');
        notificationText.className = 'notification-text';
        
        const message = document.createElement('p');
        message.innerHTML = notification.message;
        
        const time = document.createElement('div');
        time.className = 'notification-time';
        
        const notificationDate = new Date(notification.created_at);
        time.textContent = notificationDate.toLocaleString();
        
        notificationText.appendChild(message);
        notificationText.appendChild(time);
        
        notificationContent.appendChild(avatarImg);
        notificationContent.appendChild(notificationText);
        
        notificationItem.appendChild(notificationContent);
        
        notificationItem.addEventListener('click', function() {
            markNotificationAsRead(notification.id, notification.link);
        });
        
        return notificationItem;
    }
    
    function markNotificationAsRead(notificationId, link) {
        fetch('../api/mark-notification-read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `notification_id=${notificationId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = link;
            }
        });
    }
    
    function toggleNotification() {
        const postId = notifyButton.getAttribute('data-post-id');
        
        fetch('../api/toggle-notification.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${postId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                notifyButton.classList.toggle('active', data.subscribed);
                notifyButton.innerHTML = data.subscribed ? 
                    '<i class="fa-solid fa-bell"></i> Subscribed' : 
                    '<i class="fa-regular fa-bell"></i> Notify me';
            }
        });
    }
    
    function checkNotificationStatus() {
        const postId = notifyButton.getAttribute('data-post-id');
        
        fetch('../api/check-notification-status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${postId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                notifyButton.classList.toggle('active', data.subscribed);
                notifyButton.innerHTML = data.subscribed ? 
                    '<i class="fa-solid fa-bell"></i> Subscribed' : 
                    '<i class="fa-regular fa-bell"></i> Notify me';
            }
        });
    }
}); 
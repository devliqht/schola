function formatRelativeTime(dateString) {
    if (!dateString) return "Invalid date";
    const postDate = new Date(dateString.replace(" ", "T") + "Z");
    const now = new Date();

    if (isNaN(postDate.getTime())) return "Invalid date";
    const diff = Math.floor((now.getTime() - postDate.getTime()) / 1000);

    console.log("Input Timestamp:", dateString);
    console.log("Post Date (UTC):", postDate.toISOString());
    console.log("Current Time (UTC):", now.toISOString());
    console.log("Time Difference (seconds):", diff);

    if (diff < 60) return "• just now"; 
    if (diff < 3600) return `• ${Math.floor(diff / 60)}min ago`;
    if (diff < 86400) return `• ${Math.floor(diff / 3600)}h ago`;
    if (diff < 2592000) return `• ${Math.floor(diff / 86400)}d ago`;
    if (diff < 31536000) return `• ${Math.floor(diff / 2592000)}m ago`;
    return `${Math.floor(diff / 31536000)}y ago`;
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".post-date").forEach(element => {
        const timestamp = element.getAttribute("data-timestamp");
        if (timestamp) {
            element.innerText = formatRelativeTime(timestamp);
        }
    });

    setInterval(() => {
        document.querySelectorAll(".post-date").forEach(element => {
            const timestamp = element.getAttribute("data-timestamp");
            if (timestamp) element.innerText = formatRelativeTime(timestamp);
        });
    }, 30000);
});
function formatRelativeTime(dateString) {
    if (!dateString) return "Invalid date";

    // Ensure UTC parsing by appending 'Z'
    const postDate = new Date(dateString.replace(" ", "T") + "Z"); 
    const now = new Date();

    console.log("Post Date (UTC):", postDate.toISOString());
    console.log("Current Time (Local):", now.toISOString());
    console.log("Time Difference (seconds):", Math.floor((now - postDate) / 1000));

    const diff = Math.floor((now - postDate) / 1000); // Convert to seconds

    if (diff < 60) return "just now";
    if (diff < 3600) return `${Math.floor(diff / 60)}min ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}hr ago`;
    if (diff < 2592000) return `${Math.floor(diff / 86400)}d ago`;
    if (diff < 31536000) return `${Math.floor(diff / 2592000)}m ago`;
    return `${Math.floor(diff / 31536000)}y ago`;
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".post-date").forEach(element => {
        const timestamp = element.getAttribute("data-timestamp");
        if (timestamp) {
            element.innerText = formatRelativeTime(timestamp);
        }
    });
});
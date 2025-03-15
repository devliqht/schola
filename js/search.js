
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('header-search-form');
    const searchInput = document.getElementById('search-input');
    const resultsOverlay = document.getElementById('search-results-overlay');
    const resultsList = document.getElementById('search-results-list');
    const closeBtn = document.getElementById('search-close-btn');
    const searchQueryText = document.getElementById('search-query');

    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchInput.value.trim();
        searchQueryText.innerText = query;
        if (query) {
            fetch(`../validation/search.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsList.innerHTML = ''; 
                    
                    if (data.length > 0) {
                        data.forEach(item => {
                            const resultItem = document.createElement('a');
                            resultItem.classList.add('post-link');
                            resultItem.href = `post.php?id=${item.id}`;
                            
                            resultItem.innerHTML = `
                                <div class="post">
                                    <div class="flex flex-row align-center gap-4 text-white">
                                        <i class="fa-solid fa-newspaper fa-xl"></i>
                                        <div class="flex flex-col">
                                            <div class="post-title gradient-text text-base inter-700">${item.title}</div>
                                            <div class="flex">
                                                <p class="text-xs inter-300">
                                                    <span class="inter-700">${item.author}</span> ${item.created_at}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
        
                            resultsList.appendChild(resultItem);
                        });
                    } else {
                        resultsList.innerHTML = '<p>No results found</p>';
                    }
        
                    resultsOverlay.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultsList.innerHTML = '<p>Error loading results</p>';
                    resultsOverlay.style.display = 'block';
                });
        }
    });

    closeBtn.addEventListener('click', function() {
        resultsOverlay.style.display = 'none';
    });

    // Close when clicking outside
    resultsOverlay.addEventListener('click', function(e) {
        if (e.target === resultsOverlay) {
            resultsOverlay.style.display = 'none';
        }
    });
});
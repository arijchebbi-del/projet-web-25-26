document.addEventListener('DOMContentLoaded', async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('id');

    if (!userId) {
        alert("No user specified.");
        window.location.href = "/frontend/pages/recherche.html";
        return;
    }

    try {
        const response = await authApiFetch(`/profile/${userId}`);
        const data = await response.json();

        if (data.ok && data.data) {
            const user = data.data;
            document.querySelector('.nom').textContent = `${user.firstName} ${user.lastName}`;
            document.querySelector('.info-body .info-text').textContent = user.bio || "No bio provided.";
            document.querySelector('.num-id:nth-of-type(1)').textContent = user.insatienId;
            const promoEls = document.querySelectorAll('.num-id');
            if (promoEls.length > 1) {
                promoEls[1].textContent = user.promoYear || "N/A";
            }
            if (user.avatarUrl) {
                document.querySelector('.profile-pic').src = user.avatarUrl;
            }

            // Render recommendations
            const recContainer = document.getElementById('recommendationsContainer');
            if (recContainer) {
                recContainer.innerHTML = '';
                if (user.recommendations && user.recommendations.length > 0) {
                    user.recommendations.forEach(rec => {
                        const recHtml = `
                            <div class="review-card">
                              <div class="review-header">
                                <img src="${rec.author.avatarUrl || 'https://i.pravatar.cc/100?img=' + rec.author.id}">
                                  <div class="review-name">${rec.author.firstName} ${rec.author.lastName}</div>
                              </div>
                              <div class="review-text">${rec.text}</div>
                            </div>
                        `;
                        recContainer.insertAdjacentHTML('beforeend', recHtml);
                    });
                } else {
                    recContainer.innerHTML = '<p class="text-muted">No recommendations yet.</p>';
                }
            }

            const postsContainer = document.getElementById('postsContainer');
            if (postsContainer) {
                postsContainer.innerHTML = '';
                if (user.posts && user.posts.length > 0) {
                    user.posts.forEach(post => {
                        const postHtml = `
                            <div class="card mb-2" style="border-radius:10px;">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted" style="font-size:0.8rem;">
                                      ${new Date(post.createdAt).toLocaleDateString()}
                                    </h6>
                                    <p class="card-text">${post.content}</p>
                                </div>
                            </div>
                        `;
                        postsContainer.insertAdjacentHTML('beforeend', postHtml);
                    });
                } else {
                    postsContainer.innerHTML = '<p class="text-muted">No posts yet.</p>';
                }
            }

        } else {
            alert("Failed to load profile.");
        }
    } catch (error) {
        console.error("Error loading profile", error);
    }
});

async function submitRecommendation() {
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('id');
    const text = document.getElementById('recommendationText').value.trim();

    if (!text) {
        alert("Please write something first.");
        return;
    }

    try {
        const response = await authApiFetch(`/profile/${userId}/recommend`, {
            method: 'POST',
            body: JSON.stringify({ text })
        });
        const data = await response.json();

        if (data.ok) {
            alert("Recommendation submitted!");
            document.getElementById('recommendationText').value = '';
            // Close modal
            const myModal = bootstrap.Modal.getInstance(document.getElementById('recommendModal'));
            if (myModal) myModal.hide();
            
            // Reload page to show new recommendation
            window.location.reload();
        } else {
            alert("Failed: " + data.message);
        }
    } catch (error) {
        console.error("Failed to submit recommendation", error);
        alert("Network error.");
    }
}

async function submitUserProfilePost() {
    const text = document.getElementById('postContentInput').value.trim();

    if (!text) {
        alert("Please write something first.");
        return;
    }

    try {
        const response = await authApiFetch(`/posts`, {
            method: 'POST',
            body: JSON.stringify({ content: text })
        });
        const data = await response.json();

        if (data.ok) {
            document.getElementById('postContentInput').value = '';
            // Close modal
            const myModal = bootstrap.Modal.getInstance(document.getElementById('postModal'));
            if (myModal) myModal.hide();

            // Reload page to show new post
            window.location.reload();
        } else {
            alert("Failed to create post: " + data.message);
        }
    } catch (error) {
        console.error("Failed to submit post", error);
        alert("Network error.");
    }
}
document.addEventListener("DOMContentLoaded", async function () {
    const likedPostIds = [];

    function renderPost(post) {
        const blogPostsContainer = document.getElementById("blog-posts");

        // Create a post container
        const postContainer = document.createElement("div");
        postContainer.classList.add("post");
        const imageHtml = post.imagePost ? `<p style="text-align: center"><img class="post-image" src="${post.imagePost}" alt="Post Image"></p>`: ``;
        // Create post content
        const postContent = `
            <div>
                <p style="text-align: right; font-size: small; font-weight: 700"><i>${post.dateOfPost}</i></p>
                <h1><i>${post.titlePost}<i></h1>
                ${imageHtml}                
                <div class="description-container">
                    <p>${post.descriptionPost}</p>
                </div>
                <br>
                
                <div class="reaction-comment-container">
                    <div class="like-container" id="likeContainer_${post.postID}">
                        <span class="reaction-count">${post.numberReactions}</span>
                        <div class="like-button" id="likeButton_${post.postID}" onclick="handleLike(${post.postID})">
                            <span class="like-button">❤️</span>
                        </div>
                    </div>
                    <div class="comment-container">
                        <span>${post.numberComments} Comments</span><br>
                        <button class="comment-button" data-post-id="${post.postID}">Comment</button>
                    </div>
                </div>
            </div>
        `;

        // Set post content to the post container
        postContainer.innerHTML = postContent;

        // Append the post container to the blog posts container
        blogPostsContainer.appendChild(postContainer);

        // Add click event listener to each image
        const postImages = postContainer.querySelectorAll('.post-image');
        postImages.forEach(image => {
            image.addEventListener('click', () => {
                openModal(image.src);
            });
        });

        // Add click event listener to comment button
        const commentButton = postContainer.querySelector('.comment-button');
        commentButton.addEventListener('click', () => {
            handleComment(post.postID);
        });

        const likeButton = postContainer.querySelector('.like-button');
        likeButton.addEventListener('click', () => {
            handleLike(post.postID);
        });
    }


    // Render each blog post
    // fetch('get_posts.php')
    //     .then(response => response.json())
    //     .then(postsData => {
    //         const blogPostsContainer = document.getElementById("blog-posts");
    //         postsData.forEach(post => {
    //             renderPost(post);
    //             // Add a separator (hr) between posts for better visual separation
    //             const separator = document.createElement("hr");
    //             blogPostsContainer.appendChild(separator);
    //         });
    //     })
    //     .catch(error => console.error('Error fetching posts:', error));

    async function fetchPosts() {
        try {
            const response = await fetch('get_posts.php');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const postsData = await response.json();

            // Sử dụng map thay vì forEach để giữ lại mảng sau khi render
            const renderedPosts = postsData.map(post => {
                renderPost(post);
                return post;
            });

            // Thêm separator sau mỗi bài viết trừ bài viết cuối cùng
            renderedPosts.slice(0, -1).forEach(() => {
                const separator = document.createElement("hr");
                blogPostsContainer.appendChild(separator);
            });

        } catch (error) {
            console.error('Error fetching posts:', error);
        }
    }

    // Gọi hàm fetchPosts để lấy và hiển thị bài viết
    await fetchPosts();


    // Modal functions
    function openModal(imageSrc) {
        const modal = document.getElementById('myModal');
        const modalImg = document.getElementById('modalImage');
        const closeBtn = document.getElementById('closeBtn');

        modalImg.src = imageSrc;

        // Wait for the image to load before calculating its dimensions
        modalImg.onload = function () {
            modal.style.display = 'block';
            modal.style.top = '0px';
            modal.style.left = '0px';
            //modal.style.transform = 'translate(-50%, -50%)';
           // modalImg.style.top = '50px';

        };

        closeBtn.addEventListener('click', closeModal);
        window.addEventListener('click', outsideClick);
    }

    function closeModal() {
        const modal = document.getElementById('myModal');
        modal.style.display = 'none';
    }

    function outsideClick(e) {
        const modal = document.getElementById('myModal');
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    }

    function handleLike(postId) {
        const likedIndex = likedPostIds.indexOf(postId);
        require_once("connection.php");
    
        if (likedIndex === -1) {
            likedPostIds.push(postId);
            updateReactions(postId, 1); // Tăng giá trị reactions lên 1
        } else {
            likedPostIds.splice(likedIndex, 1);
            updateReactions(postId, -1); // Giảm giá trị reactions đi 1
        }
    
        updateLikeStatus(postId);
    }
    
    function updateReactions(postId, value) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "forum.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(`postId=${postId}&value=${value}`);
    }

    function handleComment(postId) {
        window.location.href = `indexCom.php?postId=${postId}`;
    }
});

function openEditForm(commentID, comment) {
    var editForm = document.getElementById("edit-form");
    var commentInput = editForm.querySelector("textarea[name='comment']");
    var commentIDInput = editForm.querySelector("input[name='commentID']");

    commentInput.value = comment;
    commentIDInput.value = commentID;

    // Hiển thị form chỉnh sửa
    editForm.style.display = "block";
}


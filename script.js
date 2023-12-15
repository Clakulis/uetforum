function toggleForm() {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    if (loginForm.style.display === 'none') {
        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
    } else {
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
    }
}

function loginForm() {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    loginForm.style.display = 'block';
    registerForm.style.display = 'none';
}

function registerForm() {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    loginForm.style.display = 'none';
    registerForm.style.display = 'block';
}

function validateYear(input) {
    const year = input.value.split('-')[0];
    if (year.length !== 4) {
        alert("Vui lòng nhập đúng 4 chữ số cho năm.");
        input.value = '';
    }
}

function openPostModal() {
    document.getElementById("post-modal").style.display = "block";
}

function editprofile(userID) {
    window.location.href = "editProfile.php?userId="+ userID;
}


function clickLogo() {
    window.location.href = "forum.php?category=recently";
}

function confirmDelete(postID, userID) {
    if(window.confirm('Bạn có chắc chắn muốn xóa bài viết này')) {
        window.location.href = 'a.php?postId='+ postID;
    } else {
        window.location.href = 'profile.php?userId='+ userID;
    }

}

// function clickPost(postID) {
//     window.location.href = "indexCom.php?postId=" + postID;
// }





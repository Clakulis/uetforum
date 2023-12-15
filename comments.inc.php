<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once("connection.php");

//hàm xử lý đăng comment
function setComments($conn, $postID) {
    if(isset($_POST['commentSubmit'])) {
        $userIDComment = $_SESSION['userID'];
        $comment = $_POST['comment'];
        $commentID = 'CMT'.str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);

        //cập nhật số cmt
        $sql = "UPDATE posts SET numberComments = numberComments + 1 WHERE postID = '$postID'";
        $result = $conn->query($sql);

        //ghi vào db
        $stmt = $conn->prepare("INSERT INTO comments (commentID, userIDComment, postIDComment, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $commentID, $userIDComment, $postID, $comment);
        $result = $stmt->execute();

        // ghi vào interact post
        $sql = "SELECT * FROM interactposts WHERE userIDInteract = '$userIDComment' AND postIDInteract = '$postID'";
        $result = $conn->query($sql);
        if($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO interactposts (userIDInteract, postIDInteract) VALUES (?, ?)");
            $stmt->bind_param("ss", $userIDComment, $postID);
            $result = $stmt->execute();
        }
        $sql = "UPDATE interactposts SET isComment = isComment + 1 WHERE userIDInteract = '$userIDComment' AND postIDInteract = '$postID'";
        $result = $conn->query($sql);


        $noticeID = 'NO'.str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

        $fullName = '';
        $userID = '';
        $fullNameQuery = "SELECT * FROM users WHERE userID = '$userIDComment'";
        $fullNameResult = $conn->query($fullNameQuery);
        if($fullNameResult->num_rows > 0) {
            $row = $fullNameResult->fetch_assoc();
            $fullName = $row['fullName'];
            $userID = $row['userID'];
        }

        $titlePost = '';
        $titlePostQuery = "SELECT titlePost FROM posts WHERE postID = '$postID'";
        $titlePostResult = $conn->query($titlePostQuery);
        if($titlePostResult->num_rows > 0) {
            $row = $titlePostResult->fetch_assoc();
            $titlePost = $row['titlePost'];
        }

        $message = 'Người dùng: '.$fullName.' đã comment bài viết '.$titlePost.' của bạn.';

        $userIDNotice = '';
        $userIDNoticeQuery = "
            SELECT userID FROM users u
            INNER JOIN posts p ON p.userIDPost = u.userID 
            WHERE postID = '$postID'";
        $userIDNoticeResult = $conn->query($userIDNoticeQuery);
        if($userIDNoticeResult->num_rows > 0) {
            $row = $userIDNoticeResult->fetch_assoc();
            $userIDNotice = $row['userID'];
        }
        //cập nhật thông báo cho người comment nếu khác chủ bài viết
        if($userIDNotice != $userIDComment) {
            $stmt = $conn->prepare("INSERT INTO notices (noticeID, userIDNotice, userIDDo, postIDNotice, commentIDNotice, message) VALUES (?, ?, ?, ?, ?, ?);");
            $stmt->bind_param("ssssss", $noticeID, $userIDNotice, $userID, $postID, $commentID, $message);
            $result = $stmt->execute();
        }

        $truyvan = "SELECT * FROM interactposts WHERE postIDInteract = '$postID' and isFollowPost = 1";
        $dapan = $conn->query($truyvan);
        if($dapan->num_rows > 0) {
            while($row = $dapan->fetch_assoc()) {
                $user = $row['userIDInteract'];
                if($user != $userIDComment) {
                    $noticeID = 'NO'.str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
                    $message = 'Người dùng: '.$fullName.' đã comment bài viết '.$titlePost.' mà bạn theo dõi.';
                    
                    if($userIDNotice != $userIDComment) {
                        $stmt = $conn->prepare("INSERT INTO notices (noticeID, userIDNotice, userIDDo, postIDNotice, commentIDNotice, message) VALUES (?, ?, ?, ?, ?, ?);");
                        $stmt->bind_param("ssssss", $noticeID, $user, $userIDComment, $postID, $commentID, $message);
                        $result = $stmt->execute();
                    }
                }
            }
        }
        
        header("Location: indexCom.php?postId=$postID");
        exit();
    }
}

//hiện khung trang cá nhân bên phải profile khi truy cập profile 1 user
function displayUserProfile($conn, $userID, $userIDNow) {
    $sql = "SELECT * FROM users WHERE userID = '$userIDNow'";
    $result = $conn->query($sql);

    if($result->num_rows > 0) {
        $userInfo = $result->fetch_assoc();
        echo "
            <div class='w3-card w3-round w3-white' >
                <div class='w3-container' style='width:250px; '>
                    <p class='w3-center'><img src='".$userInfo['linkAva']."'style='height:150px;width:150px; border-radius: 50%; object-fit: cover;display: flex;
                    flex-direction: row;margin: 3rem auto;
                    align-items: center;text-align: center;' alt='Avatar'></p>";
        
        if($userID !== $userIDNow) {
            echo "
                    <div style='text-align: center;'>
                        <a href='followUser.php?userId=".$userID."&userIDNow=".$userIDNow."' class='post-actions' style='font-size: small; font-weight: 700;'>
                            <button style='border: 2px solid; background-color: 
            ";
            $sql = "SELECT * FROM interactusers WHERE userIDInteracting = '$userID' AND userIDInteracted = '$userIDNow' AND isFollow = 1";
            $result = $conn->query($sql);
            if($result->num_rows > 0) {
                echo "##0c87eb";
            } else {
                echo "#ffffff";
            }
            echo "
                        !important '><i class='fa fa-bell' ></i></button>
                        </a>
                    </div>
            ";
        }           
                    
        echo "           
                    <p style='white-space: nowrap; overflow: hidden; text-overflow: ellipsis;'><i class='fa fa-id-card fa-fw w3-margin-right w3-text-theme'></i>".$userInfo['fullName']."</p>
                    <p ><i class='fa fa-birthday-cake fa-fw w3-margin-right w3-text-theme'></i>".$userInfo['birthday']."</p>
                    <p><i class='fa fa-venus-mars fa-fw w3-margin-right w3-text-theme'></i>".$userInfo['gender']."</p>
                    <p><i class='fa fa-users fa-fw w3-margin-right w3-text-theme'></i>Followers: ".$userInfo['followers']."</p>
                </div>
            </div>";
        if($userID === $userIDNow) {
            echo "<button type='button' class='w3-button w3-theme' style='margin-top: 20px; border-radius: 10%' onclick=\"editprofile('{$userID}')\">Edit profile</button>";
        }
        //echo $userID.' '.$userIDNow;
    } else {
        echo "User not found.";
    }
}

//load các group hiện có, có thể bấm vô để xem các bài viết theo chủ đề
function loadGroup($conn) {
    $categoryGroup = $_SESSION['category'];

    echo '
        <button onclick="redirectToForum(\'recently\')" class="w3-button w3-block w3-theme-l1 w3-left-align"  style="padding: 20px;';
    if($categoryGroup === "recently") {
        echo 'color:#fff !important; background-color:#085a9d !important';
    }
    echo '"><i class="fa fa-group fa-fw w3-margin-right"></i>Gần đây</button>';
    $sql = "SELECT * FROM groupss";
    $result = $conn->query($sql);

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '
                <button onclick="redirectToForum(\''.$row['categoryGroup'].'\')" class="w3-button w3-block w3-theme-l1 w3-left-align"  style="padding: 20px;';
            if($row['categoryGroup'] === $categoryGroup) {
                echo 'color:#fff !important; background-color:#085a9d !important';
            }
            echo '"><i class="fa fa-group fa-fw w3-margin-right"></i>'.$row['categoryGroup'].'</button>';
        }
    }
    echo
        '<script>
            function redirectToForum(categoryGroup) {
                window.location.href = "forum.php?category=" + categoryGroup + "&page=1";
            }
        </script>';
}


function upPostForum($conn, $redirectFile) {
    echo '
        <div class="w3-modal" id="post-modal">
            <div class="w3-modal-content">
                <div class="w3-container w3-padding">
                    <span class="w3-right w3-opacity">
                        <i class="fa fa-times" onclick="closePostModal()"></i>
                    </span>

                    <form action="upload_post.php" method="post" enctype="multipart/form-data">
                        <h3>ĐĂNG BÀI</h3>
                        <label>Tiêu đề:</label><br>
                        <input class="w3-input w3-border w3-padding" type="text" name="post_title" placeholder="Title" required><br>
                        <label>Description:</label><br>
                        <textarea rows="10" cols="105" style="resize: none;" class="textarea w3-border" id="myTextarea" name="post_description" required></textarea><br>
                        <label>Group:</label>';
    require_once("connection.php");
    $sql = "SELECT groupID, categoryGroup FROM groupss";
    $result = $conn->query($sql);

    if($result->num_rows > 0) {
        echo '
                        <select class="w3-select w3-border w3-margin-bottom" style="overflow:auto" id="group" name="select_group">';

        while($row = $result->fetch_assoc()) {
            $groupID = htmlspecialchars($row["groupID"]);
            $categoryGroup = htmlspecialchars($row["categoryGroup"]);
            echo '
                            <option value="'.$groupID.'">'.$categoryGroup.'</option>';
        }

        echo '
                        </select>';
    } else {
        echo "No data found";
    }
    echo '  
                        <br>
                        <label class="label1" for="file">Upload Image:</label><br>                  
                        <input class="input1" type="file" name="file" style="text-align: center;" accept="image/*"><br>
                        <button class="w3-button w3-theme w3-margin-top">Upload</button>
                    </form>
                </div>
            </div>
        </div>';
    echo'
        <script>
            function closePostModal() {
                document.getElementById("post-modal").style.display = "none";
            }
        </script>';
}

//hàm load Post của từng userID khi xem profile

function getPosts($conn, $userID, $userIDNow) {
    $sql = "SELECT * FROM posts where userIDPost = '$userIDNow' ORDER BY dateOfPost DESC";
    $result = $conn->query($sql);

    while($row = $result->fetch_assoc()) {
        $postID = $row['postID'];

        $imageHtml = isset($row['imagePost']) ? "<p style='object-fit:cover;text-align: center'><img class='post-image' src='{$row['imagePost']}' alt='Post Image' onclick='openModal(\"{$row['imagePost']}\", \"myModal_{$postID}\")'></p>" : '';
        echo "
            <div class='post w3-container w3-card w3-white w3-round w3-margin w3-padding-16' id='blog-post_{$postID}' onclick='clickPost(\"{$row['postID']}\", event)'>";
        if($userID == $userIDNow) {
            echo "
                <span class='w3-right' style='text-align: right;'>
                    <a href='confirmDelete.php?postId=".$postID."&userId=".$userID."' class='post-actions' style='font-size: small; font-weight: 700;'>
                        <button  style='border: 2px solid;'><i class='fa fa-remove'></i></button>
                    </a>";
        } else {
            echo "
                <span class='w3-right' style='text-align: right;'>
                    <a href='followPost.php?postId=".$postID."&userId=".$userID."' class='post-actions' style='font-size: small; font-weight: 700;'>
                        <button style='border: 2px solid; background-color: 
            ";
            $sql1 = "SELECT * FROM interactposts WHERE userIDInteract = '$userID' AND postIDInteract = '$postID' AND isFollowPost = 1";
            $result1 = $conn->query($sql1);
            if($result1->num_rows > 0) {
                echo "#ffff00";
            } else {
                echo "#ffffff";
            }
            echo "
                        !important '><i class='fa fa-bell' ></i></button>
                    </a>
            ";    
        }
        echo "
                    <p style='font-size: small; font-weight: 700'><i>{$row['dateOfPost']}</i></p>
                </span>
                <h1 style='display:inline-block'><i>{$row['titlePost']}<i></h1>";
        echo $imageHtml;
        echo "
                <div class='description-container' style='height: auto; max-height: 300px; resize:none; overflow-y: auto;'>
                    <p>{$row['descriptionPost']}</p>
                </div>
                <br>
                
                <div class='reaction-comment-container'>
                    <div></div>
                    <div class='comment-container'>
                        <span>{$row['numberComments']} <a href='indexCom.php?postId={$postID}' class='comment-button' data-post-id='{$postID}' >Comments</a></span><br>    
                    </div>
                </div>
            </div>
            <div id='myModal_{$postID}' class='modal1'>
                <span id='closeBtn_{$postID}' class='close' onclick='closeModal(\"myModal_{$postID}\")'>&times;</span>
                <img id='modalImage_{$postID}' style='display: block;
                margin: auto; 
                max-width: 100%;
                max-height: 100%;
                border-radius: 5%;' >
            </div>
            
        ";
    }
    echo "
            <script> 
            function confirmDelete(postID, userID, event) {
                if(window.confirm('Bạn có chắc chắn muốn xóa bài viết này')) {
                    window.location.href = 'a.php?postId=' + postID;
                } else {
                    window.location.href = 'profile.php?userId=' + userID;
                }
            }
            </script>
            <script>
                function clickPost(postID, event) {
                    if (!event.target.closest('.post-image')) {
                        window.location.href = 'indexCom.php?postId=' + postID;
                    }
                }
    
                function openModal(imageSrc, modalID) {
                    const modal = document.getElementById(modalID);
                    const modalImg = document.getElementById('modalImage_' + modalID.split('_')[1]);
    
                    modalImg.src = imageSrc;
                    
                    modalImg.onload = function () {
                        modal.style.display = 'block';
                        modal.style.top = '0px';
                        modal.style.left = '0px';
                    };
    
                    window.addEventListener('click', function(event) { outsideClick(event, modalID); });
                }
    
                function closeModal(modalID) {
                    const modal = document.getElementById(modalID);
                    modal.style.display = 'none';
                }
    
                function outsideClick(event, modalID) {
                    const modal = document.getElementById(modalID);
                    const closeBtn = document.getElementById('closeBtn_' + modalID.split('_')[1]);
                    
                    if (event.target === modal || event.target === closeBtn) {
                        modal.style.display = 'none';
                    }
                }
            </script>
        ";
}
// function confirmDelete(postID, userID) {
//     if(window.confirm('Bạn có chắc chắn muốn xóa bài viết này') {
//         window.location.href = 'a.php?postId='+ postID;
//     } else {
//         window.location.href = 'profile.php?userId='+ userID;
//     }

// }

//hàm load bài theo group ở forum
function getPostsForum($conn, $categoryGroup, $page) {
    $sql = "";
    if($categoryGroup === "search") {
        $search = $_SESSION['search'];
        $sql = "SELECT * FROM posts p
        inner join users u ON u.userID = p.userIDPost 
        WHERE titlePost LIKE '%$search%' 
            OR descriptionPost LIKE '%$search%' 
            OR dateOfPost LIKE '%$search%' 
            OR userIDPost LIKE '%$search%' 
            OR fullName LIKE '%$search%'
        ORDER BY dateOfPost DESC";
    } else if($categoryGroup === "recently") {
        $sql = "SELECT * FROM posts ORDER BY dateOfPost DESC ";
    } else {
        $sql1 = "select * from groupss WHERE categoryGroup = '$categoryGroup'";
        $result1 = $conn->query($sql1);
        $row = $result1->fetch_assoc();
        $groupID = $row['groupID'];

        $sql = "SELECT * FROM posts WHERE groupIDPost = '$groupID' ORDER BY dateOfPost DESC";
    }
    $result = $conn->query($sql);

    $postsPerPage = 5;
    $totalPosts = $result->num_rows;
    $totalPages = ceil($totalPosts / $postsPerPage);
    $startIndex = ($page - 1) * $postsPerPage;
    $sql .= " LIMIT $startIndex, $postsPerPage";

    $result = $conn->query($sql);

    while($row = $result->fetch_assoc()) {
        $postID = $row['postID'];
        $userID = $row['userIDPost'];
        $sql1 = "select * from users WHERE userID = '$userID'";
        $result1 = $conn->query($sql1);
        $row1 = $result1->fetch_assoc();
        $sql2 = "select * from groupss where groupID = '{$row['groupIDPost']}'";
        $result2 = $conn->query($sql2);
        $row2 = $result2->fetch_assoc();
        $user = $_SESSION['userID'];

        $sqlll = "SELECT * FROM interactposts WHERE userIDInteract = '$user' AND postIDInteract = '$postID' and isFollowPost = 1";
        $ans = $conn->query($sqlll);

        echo "
                <div class='post w3-container w3-card w3-white w3-round w3-margin w3-padding-16' style='margin-bottom:30px!important' id='blog-post_{$postID}'>
                    <span class='w3-right w3-opacity'";
        if($userID !== $user)
        {
                echo "
                    style='padding-top:8px'>
                        <a href='followPost.php?postId=".$postID."&userId=".$user."' class='post-actions' style='font-size: small; font-weight: 700;'>
                            <button style='border: 2px solid; background-color: 
                ";
                if($ans->num_rows > 0) {
                    echo "#0c87eb";
                } else {
                    echo "#ffffff";
                }
                echo "
                            !important '><i class='fa fa-bell' ></i></button>
                        </a><br>    
                "; 
        } else {
            echo "style='padding-top:15px'>";
        }
        echo "         
                        <i class='fa fa-thumbs-up fa-fw'></i>{$row['numberReactions']}
                        <i onclick='clickPost(\"{$row['postID']}\")' class='fa fa-comments fa-fw'></i>{$row['numberComments']}
                    </span>
                    <a href='profile.php?userId=".$row['userIDPost']."' style='text-overflow: ellipsis;text-decoration:none;'>   
                        <img src='{$row1['linkAva']}' class='w3-left w3-circle w3-margin-right' style='object-fit:cover;width:65px; height:65px; display:inline-block' alt='Avatar'>      
                    </a>
                    <div style='vertical-align: middle;display:inline-block'>
                        <button type='button' class='w3-button w3-theme-d1' onclick='redirectToForum(\"".$row2['categoryGroup']."\")'
                        style='padding:0 5px 0 5px;margin: 0;display: inline-block;'> {$row2['categoryGroup']}</button>
                        <a href='#' onclick='clickPost(\"{$row['postID']}\")' class='w3-hoverable' style='display: inline-block; margin-bottom: 0; font-size:2.5ch'>{$row['titlePost']}</a>
                        <p style='margin:0; font-style: italic;'>{$row1['fullName']} {$row['dateOfPost']}</p>
                    </div>
                </div>
          ";
    }

    for ($i = max(1, $page - 2); $i <= min(max(1, $page - 2) + 5, $totalPages); $i++) {
        echo '<a href="?category=' . $categoryGroup . '&page=' . $i . '" class="w3-button w3-theme-d1" style="padding:0 10px 0 5px;margin:1px 2px 5px 5px ;display: inline-block;';
        if($i == $page) echo 'color:#fff !important; background-color:#074b83 !important;';
        echo '">' . $i . '</a>';
    }

    echo "
        <script>
        function clickPost(postID) {
            window.location.href = 'indexCom.php?postId=' + postID;
        }
    </script>";
}

//hàm load tất cả comment của bài viết
function getComments($conn, $userID, $postID) {
    $sql = "SELECT * FROM comments WHERE postIDComment = '$postID' order by dateOfComment ASC";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        $sql1 = "SELECT * FROM users WHERE userID = \"".$row['userIDComment']."\"";
        $result1 = $conn->query($sql1);
        $row1 = $result1->fetch_assoc();

        //trả vể người được reply
        $fullNameAns = '';
        if($row['repCommentID'] !== null)
        {
            $commentID = $row['commentID'];
            $ans = "SELECT * FROM users u
            JOIN comments c ON u.userID = c.userIDComment
            JOIN comments c1 ON c.commentID = c1.repCommentID
            WHERE c1.commentID = '$commentID'";
            $resAns = $conn->query($ans);   
            $re = $resAns->fetch_assoc();
            if($re['userID'] !== $row1['userID']) $fullNameAns = " reply ". $re['fullName'];
        }

        echo "
            <div class='w3-container w3-card w3-white w3-round w3-margin'><br>
            <div id='{$row['commentID']}' href='#{$row['repCommentID']}'>
            
                <a href='profile.php?userId={$row['userIDComment']}' style='text-decoration:none'>
                    <img src='{$row1['linkAva']}' class='w3-left w3-circle w3-margin-right' style='width:65px; height:65px; display:inline-block' alt='Avatar'>
                </a>
                <div style='vertical-align: middle;display:inline-block; width:90%'>
                    <span class='w3-right w3-opacity' style='padding-top:8px'>
                        <p style='text-align: right; font-size: small; font-weight: 600'><i>{$row['dateOfComment']}</i></p>
                    </span>
                    <br>
                    <span class='user-name'><b>{$row1['fullName']}<a class='scroll-link' href='#{$row['repCommentID']}' style='text-decoration:none'>{$fullNameAns}</a></b></span><br>
                    <hr>
                    <p style='margin:0; padding-bottom: 16px; display:contents'> ".nl2br($row['comment'])."</p>
                    <br><br>
                    <div style='margin:0; padding-bottom: 16px; display: flex; justify-content: right;'>
                        <form class='reply-form w3-margin' method='POST' action='replyComment.php'> 
                            <input type='hidden' name='repCommentID' value='".$row['commentID']."'>
                            <input type='hidden' name='userIDComment' value='".$row['userIDComment']."'> 
                            <input type='hidden' name='postIDComment' value='".$row['postIDComment']."'>
                            <button class='w3-button w3-theme' type='submit' name='replyComment'>Reply</button>
                        </form>";

        if($userID == $row['userIDComment']) {
            echo "
                
                        <form class='edit-form w3-margin' method='POST' action='editcomment.php?commentId=".$row['commentID']."'> 
                            <input type='hidden' name='commentID' value='".$row['commentID']."'>
                            <input type='hidden' name='userIDComment' value='".$row['userIDComment']."'>
                            <input type='hidden' name='postIDComment' value='".$row['postIDComment']."'>
                            <input type='hidden' name='comment' value='".$row['comment']."'>
                            <button class='w3-button w3-theme'>Edit</button>
                        </form> 
                    
                        <form class='delete-form w3-margin' method='POST' action='indexCom.php?postId=".$row['postIDComment']."&id=3'> 
                            <input type='hidden' name='commentID' value='".$row['commentID']."'>
                            <input type='hidden' name='postIDComment' value='".$row['postIDComment']."'>
                            <button class='w3-button w3-theme' type = 'submit' name = 'commentDelete'>Delete</button>
                        </form>";
        }
        echo
                "   </div>
                </div>
            
            </div>
            </div>";
    }
    echo "
    <script>
        document.querySelectorAll('.scroll-link').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                console.log('clicked');
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 50, // Điều chỉnh giảm khoảng cách nếu có thanh đầu trang cố định
                        behavior: 'smooth' // Cuộn mượt
                    });
                }
            });
        });
    </script>";
}

//hàm xử lý khi nhấn nút delete
function deleteComments($conn, $userID, $postID, $commentID) {
    //xóa cmt thì xóa cả cmt tỏng post, trong notice về user, trong comments, trong interactposts
    $sql = "UPDATE posts SET numberComments = greatest(numberComments - 1, 0) WHERE postID = '$postID'";
    $result = $conn->query($sql);
    $sql = "DELETE FROM comments WHERE commentID = '$commentID'";
    $result = $conn->query($sql);
    $sql = "UPDATE interactposts SET isComment = greatest(isComment - 1, 0) WHERE userIDInteract = '$userID' AND postIDInteract = '$postID'";
    $result = $conn->query($sql);

    $stmt = $conn->prepare("DELETE FROM notices WHERE commentIDNotice = ?");
    $stmt->bind_param("s", $commentID);
    $stmt->execute();
    header("Location: indexCom.php?postId=$postID");
    exit();
}

function displayMenu($conn, $userID) {
    $sql = "SELECT * FROM users WHERE userID = '$userID'";
    $result = $conn->query($sql);
    $userInfo = $result->fetch_assoc();
    $numberNotice = 5;
    $sql1 = "SELECT * FROM notices WHERE userIDNotice = '$userID' AND userIDDo is not null AND statusReadNotice = 0 ORDER BY dateOfNotice desc";
    $result2 = $conn->query($sql1);
    $sql1 .= " LIMIT $numberNotice";
    $result1 = $conn->query($sql1);
    echo
        '
        <div class="w3-top" >
            <div class="w3-bar w3-theme-d2 w3-left-align w3-large" style="height:51px !important">
                <a href="forum.php?category=recently&page=1" class="w3-bar-item w3-button w3-padding-large w3-theme-d4" style="height:51px !important"><i class="fa fa-home w3-margin-right"></i>Home</a>
                <a onclick="openPostModal()" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white" title="Messages" style="height:51px !important"><i class="fa fa-pencil"></i></a>

                <div class="w3-dropdown-hover w3-hide-small" style="height:51px !important">
                    <button class="w3-button w3-padding-large" title="Notifications"><i class="fa fa-bell"></i><span class="w3-badge w3-right w3-small w3-green">';
    if($result2->num_rows > 0) echo $result2->num_rows;
    echo '
                    </span></button>
                    <div class="w3-dropdown-content w3-card-4 w3-bar-block" style="width:300px">';
    if($result1->num_rows > 0) {
        while($notice = $result1->fetch_assoc()) {
            if ($notice['postIDNotice'] !== null) {
                echo '
                        <a href="handle_post_notice.php?postId='.$notice['postIDNotice'].'&noticeId='.$notice['noticeID'].'" class="w3-bar-item w3-button" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 500px;">'. $notice['message'] .'</a>';
            } else if ($notice['userIDDo'] !== null) {
                echo '
                        <a href="handle_user_notice.php?userId='.$notice['userIDDo'].'&noticeId='.$notice['noticeID'].'" class="w3-bar-item w3-button" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 500px;">'. $notice['message'] .'</a>';
            }
        }
    } else {
        echo '
                        <a href="#" class="w3-bar-item w3-button">No notifications</a>';
    }                
    echo '        
                    </div>
                </div>

                <form class="w3-margin-left w3-bar-item" action="search.php" method="post" style="height:51px !important">
                    <input class="" type="text" placeholder="Search.." name="search" id="search-inp" required>
                    <button class="" type="submit"><i class="fa fa-search"></i></button>
                    </input>
                </form>

                <a href="profile.php?userId='.$userID.'" class="w3-bar-item w3-button w3-hide-small w3-right w3-padding-large w3-hover-white" style="height:51px" title="My Account" onclick="clickProfile()">
                    <img src="'.$userInfo['linkAva'].'" style="height:23px;width:23px;border-radius: 50%; object-fit: cover;display: flex;
                    flex-direction: row; align-items: center;text-align: center;" alt="Avatar">
                </a>

                <a href="logout.php?userId='.$userID.'" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white w3-right" ><i class="fa fa-sign-out"></i> Sign out</a>
            </div>
        </div>
        ';
}

function displayMenuAdmin($conn, $userID) {
$sql1 = "SELECT * from users WHERE isAccept = 0";
$sql2 = "SELECT * FROM posts WHERE isAccepted = 0";
$result1 = $conn->query($sql1);
$result2 = $conn->query($sql2);
$numUsers = $result1->num_rows;
$numPosts = $result2->num_rows;

// <div class="w3-dropdown-hover w3-hide-small" style="height:51px !important">
// <a href="admin_Users.php" class="w3-bar-item w3-button w3-hide- w3-padding-large w3-hover-white"><i class="fa fa-users"></i>
//     <span class="w3-badge w3-right w3-small w3-green">'. $numUsers.'</span>
// </a>
// </div>
echo '
    <div class="w3-top">
        <div class="w3-bar w3-theme-d2 w3-left-align w3-large" style="height:51px !important;">
            <a href="admin.php" class="w3-bar-item w3-button w3-padding-large w3-theme-d4" style="height:51px !important"><i class="fa fa-home w3-margin-right"></i>Home</a>

            <div class="w3-dropdown-hover w3-hide-small" style="height:51px !important">
                <a href="admin_Posts.php" class="w3-bar-item w3-button w3-hide- w3-padding-large w3-hover-white"><i class="fa fa-book"></i>
                    <span class="w3-badge w3-right w3-small w3-green">'.$numPosts.'</span>
                </a>
            </div>
            <div class="w3-dropdown-hover w3-hide-small" style="height:51px !important">
                <a href="admin_Groups.php" class="w3-bar-item w3-button w3-hide- w3-padding-large w3-hover-white"><i class="fa fa-plus"></i>
                    <span class="w3-badge w3-right w3-small w3-green"><i class="fa fa-group"></i></span>
                </a>
            </div>
            <a href="logout.php?userId='.$userID.'" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-hover-white w3-right"><i
                class="fa fa-sign-out"></i>Sign out</a>
        </div>
    </div>
';
}

function checkUser($conn) {
    $sql1 = "SELECT * from users WHERE isAccept = 0";
    $result1 = $conn->query($sql1);

    if($result1->num_rows > 0) {
        while($row = $result1->fetch_assoc()) {
            echo "
                <div class='post'>
                    <div style='text-align: right;'>
                        <a href='processUsers.php?userId=".$row['userID']."&id=del' class='post-actions' style='font-size: small; font-weight: 700; display: inline-block;'>
                            <button style='border: 2px solid; background-color: #55ff99!important '><i class='fa fa-remove' ></i></button>
                        </a>
                        <a href='processUsers.php?userId=".$row['userID']."&id=acp' class='post-actions' style='font-size: small; font-weight: 700; display: inline-block;'>
                            <button style='border: 2px solid; background-color: #55ff99!important '><i class='fa fa-check' ></i></button>
                        </a>
                    </div>
                    <a style='text-overflow: ellipsis;text-decoration:none;'>
                        <div class='comment-container' style='display: flex; align-items: center;'>
                            <img src='{$row['linkAva']}' class='w3-circle' style='height:60px;width:60px;border-radius: 50%; object-fit: cover; margin-right: 10px;' alt='Avatar'>
                            <div style='text-align: left;'>     
                                <span class='user-name'>".$row['fullName']."</span><br>
                            </div>
                        </div>
                    </a>
                </div>";
        }
    }    
}

function checkPost($conn) {
    $sql1 = "SELECT * from posts WHERE isAccepted = 0  ORDER BY dateOfPost DESC";
    $result1 = $conn->query($sql1);

    if($result1->num_rows > 0) {
        while($row = $result1->fetch_assoc()) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE userID = ?");
            $stmt->bind_param("s", $row['userIDPost']);
                $stmt->execute();
            $result = $stmt->get_result();
            $row1 = $result->fetch_assoc();
            echo "
                <div class='post'>
                    <div style='text-align: right;'>
                        <a href='processPosts.php?postId=".$row['postID']."&id=del' class='post-actions' style='font-size: small; font-weight: 700; display: inline-block;'>
                            <button style='border: 2px solid; background-color: #55ff99!important '><i class='fa fa-remove' ></i></button>
                        </a>
                        <a href='processPosts.php?postId=".$row['postID']."&id=acp' class='post-actions' style='font-size: small; font-weight: 700; display: inline-block;'>
                            <button style='border: 2px solid; background-color: #55ff99!important '><i class='fa fa-check' ></i></button>
                        </a>
                    </div>
                    <a style='text-overflow: ellipsis;text-decoration:none;'>
                        <div class='comment-container' style='display: flex; align-items: center;'>
                            <img src='{$row1['linkAva']}' class='w3-circle' style='height:60px;width:60px;border-radius: 50%; object-fit: cover; margin-right: 10px;' alt='Avatar'>
                            <div style='text-align: left;'>     
                                <span class='user-name'>".$row1['fullName']."</span><br>
                            </div>
                        </div>
                    </a>
                    <h1><i>{$row['titlePost']}<i></h1>
                    <div class='description-container' style='height: auto; max-height: 300px; resize:none; overflow-y: auto;'>
                        <p>{$row['descriptionPost']}</p>
                    </div>
                    <br>
                </div>";
        }
    } 
}
function checkGroup($conn) {
    $sql = "SELECT * from groupss";
    $result = $conn->query($sql);

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "
                <div class='post'>
                    <div style='text-align: right;'>
                        <a href='processGroup.php?id=del&categoryGroup=".$row['categoryGroup']."' style='font-size: small; font-weight: 700; display: inline-block;'>
                            <button style='border: 2px solid; background-color: red!important '><i class='fa fa-remove' ></i></button>
                        </a>
                    </div>
                    <a style='text-overflow: ellipsis;text-decoration:none;'>
                        <div class='comment-container' style='display: flex; align-items: center;'>
                            <div style='text-align: left;'>     
                                <span class='user-name'>".$row['categoryGroup']."</span><br>
                            </div>
                        </div>
                    </a>
                    <br>
                </div>";
        }
    } 
}
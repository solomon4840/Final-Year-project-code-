<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require '../../php/db.php';

// Get the logged-in user's email
$userEmail = $_SESSION['email'];

// Fetch projects where the user is a collaborator
$projectsQuery = "SELECT p.project_id, p.name 
                  FROM projects p
                  JOIN project_collaborators pc ON p.project_id = pc.project_id
                  WHERE pc.collaborator_email = ?";
$stmt = $conn->prepare($projectsQuery);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$projectsResult = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Portal - Messaging</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
        }

        .messaging-container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 25%;
            background: #f4f4f4;
            padding: 20px;
            overflow-y: auto;
            border-right: 2px solid #ddd;
        }

        .sidebar h3 {
            margin-bottom: 10px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar li {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
            transition: 0.3s;
        }

        .sidebar li:hover {
            background: #ddd;
        }

        .chatbox {
            width: 75%;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 15px;
            background: #007bff;
            color: white;
            text-align: center;
        }

        .chat-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background: #fff;
            max-height: 500px;
        }

        .chat-messages div {
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            max-width: 70%;
            word-wrap: break-word;
        }

        .sent {
            background: #007bff;
            color: white;
            text-align: right;
            margin-left: auto;
        }

        .received {
            background: #f1f1f1;
            text-align: left;
        }

        #send-message-form {
            display: flex;
            padding: 10px;
            background: #f4f4f4;
            border-top: 2px solid #ddd;
        }

        #send-message-form input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #send-message-form button {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            margin-left: 5px;
            border-radius: 5px;
        }

        #send-message-form button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

    <div class="messaging-container">
        <!-- Sidebar for Users & Groups -->
        <div class="sidebar">
            <h3>Direct Messages</h3>
            <ul id="direct-messages"></ul>

            <h3>Group Messages</h3>
            <ul id="group-messages"></ul>
        </div>

        <!-- Chat Window -->
        <div class="chatbox">
            <div class="chat-header">
                <h2 id="chat-title">Select a chat</h2>
            </div>
            
            <div class="chat-messages" id="chat-messages"></div>
            
            <form id="send-message-form">
                <input type="hidden" id="receiver_email" name="receiver_email">
                <input type="hidden" id="project_id" name="project_id">
                <input type="text" id="message" name="message" placeholder="Type a message..." required>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            loadDirectMessages();
            loadGroupMessages();

            function loadDirectMessages() {
                $.ajax({
                    url: '../php/dashboards/get_messages.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#direct-messages').empty();
                        data.forEach(function (message) {
                            $('#direct-messages').append(`
                                <li class="direct-message" data-email="${message.sender_email}">
                                    ${message.sender_email}
                                </li>
                            `);
                        });
                    },
                    error: function () {
                        console.log("Error loading direct messages.");
                    }
                });
            }

            function loadGroupMessages() {
                $.ajax({
                    url: '../php/dashboards/get_group_messages.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#group-messages').empty();
                        data.forEach(function (message) {
                            $('#group-messages').append(`
                                <li class="group-message" data-project-id="${message.project_id}">
                                    Project ${message.project_id}
                                </li>
                            `);
                        });
                    },
                    error: function () {
                        console.log("Error loading group messages.");
                    }
                });
            }

            $(document).on('click', '.direct-message', function () {
                let receiverEmail = $(this).data('email');
                $('#receiver_email').val(receiverEmail);
                $('#project_id').val('');
                $('#chat-title').text('Chat with ' + receiverEmail);
                loadChatMessages(receiverEmail, null);
            });

            $(document).on('click', '.group-message', function () {
                let projectId = $(this).data('project-id');
                $('#receiver_email').val('');
                $('#project_id').val(projectId);
                $('#chat-title').text('Group Chat: Project ' + projectId);
                loadChatMessages(null, projectId);
            });

            function loadChatMessages(receiverEmail, projectId) {
                let url = receiverEmail
                    ? `../php/messaging/get_messages.php?receiver_email=${receiverEmail}`
                    : `../php/messaging/get_group_messages.php?project_id=${projectId}`;

                $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#chat-messages').empty();
                        data.forEach(function (msg) {
                            let cssClass = msg.sender_email === $('#receiver_email').val() ? 'sent' : 'received';
                            $('#chat-messages').append(`
                                <div class="${cssClass}">${msg.sender_email}: ${msg.message}</div>
                            `);
                        });
                    },
                    error: function () {
                        console.log("Error loading chat messages.");
                    }
                });
            }

            $('#send-message-form').submit(function (event) {
                event.preventDefault();
                $.ajax({
                    url: '../../php/common_functionalities/send_message.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        let result = JSON.parse(response);
                        if (result.status === "success") {
                            $('#message').val('');
                            let receiverEmail = $('#receiver_email').val();
                            let projectId = $('#project_id').val();
                            loadChatMessages(receiverEmail, projectId);
                        } else {
                            alert("Message sending failed.");
                        }
                    },
                    error: function () {
                        alert("Error: Could not send message.");
                    }
                });
            });

            setInterval(function () {
                let receiverEmail = $('#receiver_email').val();
                let projectId = $('#project_id').val();
                if (receiverEmail || projectId) {
                    loadChatMessages(receiverEmail, projectId);
                }
            }, 3000);
        });
    </script>

</body>
</html>

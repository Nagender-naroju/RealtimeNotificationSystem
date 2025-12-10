<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Realtime Messages</title>

  <!-- Pusher & Axios -->
  <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: "Inter", sans-serif;
      background: #f3f4f6;
      margin: 0;
      padding: 40px;
      display: flex;
      justify-content: center;
    }

    .container {
      width: 100%;
      max-width: 700px;
      background: #ffffff;
      padding: 25px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    h2 {
      text-align: center;
      margin-bottom: 15px;
      font-weight: 600;
    }

    #messages {
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 15px;
      height: 350px;
      overflow-y: auto;
      background: #fafafa;
    }

    .msg {
      background: white;
      margin-bottom: 12px;
      padding: 12px 16px;
      border-radius: 10px;
      border-left: 4px solid #4f46e5;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      animation: fadeIn 0.3s ease-in-out;
    }

    .msg strong {
      color: #4f46e5;
    }

    .msg small {
      color: #6b7280;
      font-size: 11px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(5px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* Form Styling */
    form {
      margin-top: 20px;
      display: flex;
      gap: 10px;
      align-items: center;
    }

    input, button {
      font-family: inherit;
    }

    input[type="number"], input[type="text"], input[type="search"], input[type="email"] {
      padding: 10px 12px;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      width: 20%;
    }

    #messageInput {
      width: 65%;
    }

    button {
      padding: 11px 18px;
      background: #4f46e5;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      transition: 0.2s ease-in-out;
    }

    button:hover {
      background: #4338ca;
    }
  </style>
</head>

<body>

<div class="container">

  <h2>Realtime Messaging</h2>

  <div id="messages"></div>

  <form id="sendForm">
    <input type="number" name="sender_id" id="sender_id" placeholder="Sender ID" />
    <input type="text" id="messageInput" placeholder="Type your message..." />
    <button type="submit">Send</button>
  </form>

</div>

<script>

  // Initialize Pusher
  const pusher = new Pusher('cafe9d8b1ea9de4a9286', {
    cluster: 'ap2',
    forceTLS: true
  });

  const channel = pusher.subscribe('messages.channel');

  channel.bind('message.received', function(data) {
    console.log("Broadcast received:", data);

    if (!data || !data.messagePayload) return;

    appendMessage(data.messagePayload);
  });

  function appendMessage(payload) {
    const container = document.getElementById('messages');
    const div = document.createElement('div');
    div.className = 'msg';
    div.innerHTML = `
      <strong>Sender:</strong> ${payload.sender_id} <br/>
      <div style="margin-top:5px; margin-bottom:5px;">${payload.body}</div>
      <small>${payload.created_at}</small>
    `;
    container.prepend(div);
  }

  // Send message
  document.getElementById('sendForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const senderId = document.getElementById('sender_id').value;
    const message = document.getElementById('messageInput').value;

    if (!message) return;

    try {
      const res = await axios.post('/api/messages', {
        sender_id: senderId,
        message: message
      });

      document.getElementById('messageInput').value = '';
      document.getElementById('sender_id').value = '';

      console.log("Message queued:", res.data);
    } catch (error) {
      console.error("Error:", error.response?.data || error);
      alert("Error sending message");
    }
  });

</script>

</body>
</html>

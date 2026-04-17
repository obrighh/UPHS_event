<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chatbot with API</title>
<style>
  * {margin:0; padding:0; box-sizing:border-box; font-family:"Poppins",sans-serif;}
  body {
    height:100vh;
    background:linear-gradient(135deg, #74ABE2, #5563DE);
    display:flex;
    justify-content:center;
    align-items:center;
  }
  /* Floating Button */
  .chatbot-btn {
    position:fixed;
    bottom:25px;
    right:25px;
    background:#0078ff;
    color:#fff;
    border:none;
    border-radius:50%;
    width:65px; height:65px;
    font-size:30px;
    cursor:pointer;
    box-shadow:0 4px 15px rgba(0,0,0,0.3);
    display:flex; align-items:center; justify-content:center;
    transition:0.3s ease; z-index:1000;
  }
  .chatbot-btn:hover {background:#005edc; transform:scale(1.1);}
  /* Chat Container */
  .chat-container {
    position:fixed; bottom:100px; right:25px;
    width:350px; max-width:90%; height:480px;
    background:rgba(255,255,255,0.95);
    border-radius:15px; box-shadow:0 8px 25px rgba(0,0,0,0.3);
    display:none; flex-direction:column; overflow:hidden; z-index:999;
    backdrop-filter:blur(10px);
  }
  .chat-header {
    background:#0078ff; color:white; text-align:center; padding:15px; font-weight:bold;
  }
  .chat-messages {
    flex:1; padding:10px; overflow-y:auto; background:#f7f7f7; display:flex; flex-direction:column;
  }
  .message {
    margin:8px 0; padding:10px 14px; border-radius:10px;
    max-width:75%; line-height:1.4; font-size:14px;
  }
  .bot {background:#e4e6eb; align-self:flex-start;}
  .user {background:#0078ff; color:#fff; align-self:flex-end;}
  .chat-input {display:flex; border-top:1px solid #ddd; background:#fff; padding:8px;}
  .chat-input input {
    flex:1; padding:10px; border:none; outline:none;
    border-radius:8px; background:#f0f0f0;
  }
  .chat-input button {
    background:#0078ff; color:white; border:none;
    margin-left:10px; padding:10px 14px; border-radius:8px; cursor:pointer;
    transition:0.3s;
  }
  .chat-input button:hover {background:#005edc;}
  @media (max-width:500px){
    .chat-container{right:10px; bottom:90px; width:90%; height:420px;}
    .chatbot-btn{width:55px; height:55px; font-size:24px;}
  }
</style>
</head>
<body>

<!-- Floating Chat Button -->
<button class="chatbot-btn" id="chatbotBtn">💬</button>

<!-- Chat Window -->
<div class="chat-container" id="chatContainer">
  <div class="chat-header">Chatbot Assistant 🤖</div>
  <div class="chat-messages" id="chatMessages">
    <div class="message bot">Hello! How can I help you today?</div>
  </div>
  <div class="chat-input">
    <input type="text" id="userInput" placeholder="Type a message..." />
    <button id="sendBtn">Send</button>
  </div>
</div>

<script>
const chatBtn = document.getElementById("chatbotBtn");
const chatContainer = document.getElementById("chatContainer");
const sendBtn = document.getElementById("sendBtn");
const chatMessages = document.getElementById("chatMessages");
const userInput = document.getElementById("userInput");

// Toggle chat window
chatBtn.addEventListener("click", () => {
  chatContainer.style.display = 
    chatContainer.style.display === "flex" ? "none" : "flex";
});

// Send message
sendBtn.addEventListener("click", sendMessage);
userInput.addEventListener("keypress", e => { if(e.key==="Enter") sendMessage(); });

async function sendMessage() {
  const userMsg = userInput.value.trim();
  if (userMsg === "") return;

  appendMessage(userMsg, "user");
  userInput.value = "";

  appendMessage("Typing...", "bot");
  
  // --- API CALL SECTION ---
  try {
    // Example API call (you can replace this URL with your chatbot backend)
    const response = await fetch("https://api.openai.com/v1/chat/completions", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": "Bearer sk-...c48A" // 🔑 put your API key here
      },
      body: JSON.stringify({
        model: "gpt-3.5-turbo",
        messages: [{ role: "user", content: userMsg }]
      })
    });

    const data = await response.json();
    const botReply = data.choices?.[0]?.message?.content || "Sorry, I didn’t get that.";
    removeTyping();
    appendMessage(botReply, "bot");
  } catch (error) {
    removeTyping();
    appendMessage("Error connecting to chatbot API 😞", "bot");
    console.error(error);
  }
}

function appendMessage(text, sender) {
  const msg = document.createElement("div");
  msg.classList.add("message", sender);
  msg.textContent = text;
  chatMessages.appendChild(msg);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

function removeTyping() {
  const typing = [...chatMessages.querySelectorAll(".bot")].pop();
  if (typing && typing.textContent === "Typing...") typing.remove();
}
</script>

</body>
</html>

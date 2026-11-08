<!-- =============================================
     Chatbot Widget HTML
     Floating chat button + chat window
     ============================================= -->

<!-- Chat Toggle Button -->
<button class="chatbot-toggle" id="chatToggle" onclick="toggleChat()" title="Ask ParkBot Assistant">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
</button>

<!-- Chat Window -->
<div class="chatbot-window" id="chatWindow">
    <div class="chatbot-header">
        <h4>
            <span style="width:8px; height:8px; border-radius:50%; background:var(--success); box-shadow:0 0 6px var(--success);"></span>
            ParkBot AI Assistant
        </h4>
        <button class="close-chat" onclick="toggleChat()" title="Close Chat">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <div class="chatbot-messages" id="chatMessages">
        <!-- Messages appear dynamically -->
    </div>

    <!-- Suggested Quick Prompts -->
    <div class="suggested-questions" id="suggestedQuestions">
        <button class="suggested-btn" onclick="askQuestion('How do I book a slot?')">Book a Bay</button>
        <button class="suggested-btn" onclick="askQuestion('How can I cancel my booking?')">Cancel Pass</button>
        <button class="suggested-btn" onclick="askQuestion('Explain this project for viva')">Viva Help</button>
        <button class="suggested-btn" onclick="askQuestion('Explain database')">Database</button>
    </div>

    <div class="chatbot-input">
        <input type="text" id="chatInput" placeholder="Ask about bays, permits, viva..." 
               onkeypress="if(event.key === 'Enter') sendMessage()">
        <button onclick="sendMessage()" title="Send Message">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
        </button>
    </div>
</div>

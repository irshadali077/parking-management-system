// =============================================
// ParkBot - Offline AI Chatbot
// Works completely offline using keyword matching
// No external APIs used
// =============================================

let welcomeShown = false;

/**
 * Knowledge base - Array of question-answer pairs
 */
const knowledgeBase = [
    {
        keywords: ["book", "slot", "how to book", "reserve", "parking", "bay"],
        answer: "To reserve a parking bay:\n1. Click 'Reserve Bay' from the top navigation.\n2. Filter by Zone A (North Gate) or Zone B (East Wing).\n3. Green bays with active sensor lights are free.\n4. Click on any green bay to confirm your reservation.\n5. An official digital pass with QR code will be generated instantly!\n\nNote: Each driver is allocated one active bay permit at a time."
    },
    {
        keywords: ["cancel", "booking", "cancel booking", "release", "checkout"],
        answer: "To release or check out from your bay:\n1. Open your Dashboard — your Digital Parking Pass is displayed at the top.\n2. Click the 'Release & Exit Bay' button.\n3. Confirm check-out. The bay will immediately free up for other drivers."
    },
    {
        keywords: ["login", "log in", "sign in", "how to login"],
        answer: "To sign in:\n1. Go to the login page.\n2. Enter your registered campus email address and password.\n3. Click 'Sign In to Portal'.\n\nIf you are evaluating for college demo, use the quick 'Auto-Fill' button to test instantly."
    },
    {
        keywords: ["signup", "sign up", "register", "create account"],
        answer: "To register as a campus driver:\n1. Go to the Driver Registration page.\n2. Fill in your Full Name, Campus ID, Email, and Phone.\n3. Create a secure password (min. 6 characters).\n4. Select a security question for password recovery.\n5. Click 'Register Driver Permit Account'."
    },
    {
        keywords: ["forgot", "password", "reset password", "lost password"],
        answer: "To reset your password:\n1. Click 'Forgot?' on the login screen.\n2. Enter your registered campus email.\n3. Answer your chosen security question.\n4. Enter and confirm your new password.\n\nNo external email server is required; recovery is validated instantly through your confidential answer."
    },
    {
        keywords: ["available", "free", "green"],
        answer: "An 'Available' bay (illuminated green) means the ultrasonic bay sensor detects no vehicle and the space is ready for parking. Selecting it locks the bay exclusively to your permit."
    },
    {
        keywords: ["occupied", "parked", "red"],
        answer: "An 'Occupied' bay (illuminated red) indicates that another student or staff member has reserved and checked into that space. It cannot be booked until released."
    },
    {
        keywords: ["admin", "admin panel", "staff"],
        answer: "The Admin Operations Console allows campus security officers to:\n• Monitor real-time garage utilization percentage\n• Inspect registered drivers and contact logs\n• View the Master Bookings Ledger\n• Add new physical parking bays (e.g. VIP or Zone C)\n• Override bay occupancy manually\n\nDefault Admin Credentials: admin / admin123"
    },
    {
        keywords: ["viva", "explain project", "project explanation", "mini project"],
        answer: "ParkEase Project Viva Summary:\n\n• Concept: Automated Smart Campus Parking Management Platform.\n• Core Problem Solved: Eliminates campus traffic congestion, manual logbooks, and double-booking.\n• Architecture: Full-stack PHP application with MySQL relational database.\n• Security: Bcrypt password hashing, Prepared SQL Statements (zero SQL injection), Session-based route guards.\n• Database: 4 normalized tables (users, admin, slots, bookings) with foreign keys and cascade rules.\n• Real-World Features: Ultrasonic sensor simulation, digital boarding ticket passes, offline AI guidance."
    },
    {
        keywords: ["database", "db structure", "tables"],
        answer: "Database Structure (parking_db):\n\n1. users — Stores driver profile, hashed credentials, security question.\n2. admin — Dedicated credentials for facility administrators.\n3. slots — Real-time bay inventory and sensor occupancy status.\n4. bookings — Relational transaction logs linking users to bays with timestamps."
    },
    {
        keywords: ["sql injection", "security", "prepared statement"],
        answer: "SQL Injection Prevention:\nAll queries use MySQLi Prepared Statements ($conn->prepare) with bind_param(). User input is treated strictly as data rather than executable SQL code, making SQL injection impossible."
    },
    {
        keywords: ["hello", "hi", "hey"],
        answer: "Hello! Welcome to ParkEase Virtual Assistant. I am here to assist with campus parking, pass issuance, admin tools, or viva questions. How can I help you today?"
    },
    {
        keywords: ["thank", "thanks"],
        answer: "You are welcome! If you need any assistance with parking permits or project questions, feel free to ask anytime."
    }
];

function toggleChat() {
    const chatWindow = document.getElementById('chatWindow');
    chatWindow.classList.toggle('open');

    if (!welcomeShown && chatWindow.classList.contains('open')) {
        welcomeShown = true;
        setTimeout(() => {
            addBotMessage("Welcome to ParkEase! I am your 24/7 campus mobility assistant. Ask me anything about bay reservations, passes, or viva questions.");
        }, 300);
    }
}

function sendMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    if (message === '') return;

    addUserMessage(message);
    input.value = '';

    setTimeout(() => {
        const response = getResponse(message);
        addBotMessage(response);
    }, 450);
}

function askQuestion(question) {
    addUserMessage(question);
    setTimeout(() => {
        const response = getResponse(question);
        addBotMessage(response);
    }, 400);
}

function getResponse(userMessage) {
    const message = userMessage.toLowerCase();
    let bestMatch = null;
    let bestScore = 0;

    for (const entry of knowledgeBase) {
        let score = 0;
        for (const keyword of entry.keywords) {
            if (message.includes(keyword.toLowerCase())) {
                score += keyword.length;
            }
        }
        if (score > bestScore) {
            bestScore = score;
            bestMatch = entry;
        }
    }

    if (bestMatch && bestScore > 0) {
        return bestMatch.answer;
    }

    return "I couldn't find an exact match for that query. You can ask about: booking a bay, cancellation, admin access, database schema, or viva explanations!";
}

function addUserMessage(text) {
    const messagesDiv = document.getElementById('chatMessages');
    const msgDiv = document.createElement('div');
    msgDiv.className = 'chat-message user';
    msgDiv.textContent = text;
    messagesDiv.appendChild(msgDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function addBotMessage(text) {
    const messagesDiv = document.getElementById('chatMessages');
    const msgDiv = document.createElement('div');
    msgDiv.className = 'chat-message bot';
    msgDiv.innerHTML = text.replace(/\n/g, '<br>');
    messagesDiv.appendChild(msgDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

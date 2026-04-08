<!-- STEMAN AI Assistant Floating Bubble -->
<div id="ai-assistant-wrapper" style="position: fixed; bottom: 20px; right: 20px; z-index: 99999;">
    <!-- Chat Window (Initial status: Hidden) -->
    <div id="ai-chat-window" class="shadow-lg overflow-hidden d-none animate__animated" style="width: 350px; border-radius: 24px; background: rgba(255,255,255,0.85); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.4); margin-bottom: 20px;">
        <!-- Header -->
        <div class="p-3 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <div class="d-flex align-items-center">
                <div class="bg-warning rounded-circle p-2 me-2 shadow-sm">
                    <i class="bi bi-robot text-dark"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Steman AI Buddy</h6>
                    <small class="opacity-75" style="font-size: 0.7rem;">⚡ Online & Helpful</small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white shadow-none" onclick="toggleAIChat()" style="font-size: 0.7rem;"></button>
        </div>

        <!-- Chat Area -->
        <div id="ai-chat-body" class="p-3" style="height: 350px; overflow-y: auto; background: rgba(255,255,255,0.5);">
            <div class="ai-msg mb-4">
                <div class="p-2 px-3 rounded-4 bg-light small shadow-sm d-inline-block" style="max-width: 85%;">
                    Halo Kak! Saya Steman AI. Ada yang bisa saya bantu seputar portal atau jaringan alumni SMKN 2? 👋
                </div>
                <div class="text-muted mt-1" style="font-size: 0.6rem;">Just now</div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-3 border-top bg-white bg-opacity-50">
            <div class="input-group">
                <input type="text" id="ai-chat-input" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 shadow-none" placeholder="Tanya sesuatu..." onkeypress="handleChatEnter(event)">
                <button class="btn btn-warning btn-sm rounded-circle ms-2" onclick="sendAIChat()" style="width: 32px; height: 32px;">
                    <i class="bi bi-send-fill text-dark" style="font-size: 0.8rem;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Trigger Button -->
    <button id="ai-chat-trigger" class="btn btn-warning rounded-circle shadow-lg d-flex align-items-center justify-content-center animate__animated animate__pulse animate__infinite" 
            onclick="toggleAIChat()" style="width: 60px; height: 60px; border: 4px solid white;">
        <i class="bi bi-robot fs-3 text-dark"></i>
    </button>
</div>

<script>
    function toggleAIChat() {
        const window = document.getElementById('ai-chat-window');
        const trigger = document.getElementById('ai-chat-trigger');
        
        if (window.classList.contains('d-none')) {
            window.classList.remove('d-none');
            window.classList.add('animate__fadeInUp');
            trigger.classList.remove('animate__infinite');
            document.getElementById('ai-chat-input').focus();
        } else {
            window.classList.add('animate__fadeOutDown');
            setTimeout(() => {
                window.classList.remove('animate__fadeOutDown');
                window.classList.add('d-none');
                trigger.classList.add('animate__infinite');
            }, 300);
        }
    }

    function handleChatEnter(e) {
        if (e.key === 'Enter') sendAIChat();
    }

    async function sendAIChat() {
        const input = document.getElementById('ai-chat-input');
        const body = document.getElementById('ai-chat-body');
        const msg = input.value.trim();
        
        if (!msg) return;

        // Append User Message
        const userDiv = document.createElement('div');
        userDiv.className = 'user-msg mb-4 text-end';
        userDiv.innerHTML = `
            <div class="p-2 px-3 rounded-4 bg-primary text-white small shadow-sm d-inline-block" style="max-width: 85%;">
                ${msg}
            </div>
        `;
        body.appendChild(userDiv);
        input.value = '';
        body.scrollTop = body.scrollHeight;

        // Append Loading Typing
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'ai-loading';
        loadingDiv.className = 'ai-msg mb-4';
        loadingDiv.innerHTML = `
            <div class="p-2 px-3 rounded-4 bg-light small shadow-sm d-inline-block">
                <div class="spinner-grow spinner-grow-sm text-warning" role="status"></div> Sedang berpikir...
            </div>
        `;
        body.appendChild(loadingDiv);
        body.scrollTop = body.scrollHeight;

        try {
            const resp = await fetch('/api/ai/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message: msg })
            });
            const data = await resp.json();
            
            document.getElementById('ai-loading').remove();
            
            // Append AI Message
            const aiDiv = document.createElement('div');
            aiDiv.className = 'ai-msg mb-4';
            aiDiv.innerHTML = `
                <div class="p-2 px-3 rounded-4 bg-light small shadow-sm d-inline-block" style="max-width: 85%;">
                    ${data.reply}
                </div>
            `;
            body.appendChild(aiDiv);
            body.scrollTop = body.scrollHeight;

        } catch (e) {
            document.getElementById('ai-loading').remove();
            const errDiv = document.createElement('div');
            errDiv.className = 'text-center small text-danger mb-4 opacity-75';
            errDiv.innerHTML = 'Maaf, koneksi terputus. Coba lagi nanti!';
            body.appendChild(errDiv);
        }
    }
</script>

<style>
    #ai-assistant-wrapper .rounded-4 { border-radius: 18px !important; }
    #ai-chat-body::-webkit-scrollbar { width: 4px; }
    #ai-chat-body::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
</style>

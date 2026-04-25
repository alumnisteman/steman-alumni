@extends('layouts.app')

@section('title', 'Direct Messages')

@push('styles')
<style>
    body {
        background-color: #f8f9fa;
    }
    .chat-container {
        height: calc(100vh - 120px);
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .conversations-list {
        height: 100%;
        overflow-y: auto;
        border-right: 1px solid #eee;
    }
    .chat-window {
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #fafbfe;
    }
    .chat-messages {
        flex-grow: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .message-bubble {
        max-width: 75%;
        padding: 0.75rem 1.2rem;
        border-radius: 1.5rem;
        position: relative;
        font-size: 0.95rem;
        line-height: 1.4;
    }
    .message-mine {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 0.25rem;
    }
    .message-theirs {
        background: white;
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 0.25rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
    }
    .message-time {
        font-size: 0.7rem;
        opacity: 0.7;
        margin-top: 0.25rem;
    }
    .message-mine .message-time {
        text-align: right;
        color: rgba(255,255,255,0.8);
    }
    .message-theirs .message-time {
        color: #999;
    }
    .chat-input-area {
        padding: 1rem;
        background: white;
        border-top: 1px solid #eee;
    }
    .conversation-item {
        padding: 1rem;
        border-bottom: 1px solid #f8f9fa;
        cursor: pointer;
        transition: all 0.2s;
    }
    .conversation-item:hover {
        background: #f8f9fa;
    }
    .conversation-item.active {
        background: #eef2fa;
        border-left: 4px solid #0d6efd;
    }
    .unread-badge {
        width: 20px;
        height: 20px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: bold;
    }
    .empty-chat {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #adb5bd;
    }
    .group:hover .group-hover-opacity-100 {
        opacity: 1 !important;
    }
    .transition-opacity {
        transition: opacity 0.2s;
    }
    .message-bubble {
        position: relative;
    }
    /* Mobile specific: always show menu since there is no hover */
    @media (max-width: 768px) {
        .group-hover-opacity-100 {
            opacity: 1 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-lg-5">
    <div class="row chat-container g-0">
        {{-- Sidebar Conversations --}}
        <div class="col-md-4 col-lg-3 conversations-list bg-white" id="conversations-wrapper">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Pesan</h5>
                <button class="btn btn-light btn-sm rounded-circle"><i class="bi bi-pencil-square"></i></button>
            </div>
            
            <div id="conversations-list">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary spinner-border-sm"></div>
                </div>
            </div>
        </div>

        {{-- Main Chat Window --}}
        <div class="col-md-8 col-lg-9 chat-window" id="chat-window-wrapper">
            <div class="empty-chat" id="empty-chat-state">
                <i class="bi bi-chat-dots fs-1 mb-3 text-primary opacity-50"></i>
                <h5 class="fw-bold text-dark">Mulai Obrolan</h5>
                <p>Pilih percakapan dari samping atau kirim pesan baru ke alumni.</p>
            </div>

            <div class="d-none h-100 d-flex flex-column" id="active-chat-state">
                {{-- Header --}}
                <div class="p-3 bg-white border-bottom d-flex align-items-center gap-3">
                    <button class="btn btn-light d-md-none rounded-circle" onclick="closeChatMobile()"><i class="bi bi-arrow-left"></i></button>
                    <img src="" id="chat-header-avatar" class="rounded-circle object-fit-cover" width="40" height="40">
                    <div>
                        <h6 class="mb-0 fw-bold" id="chat-header-name">Nama Alumni</h6>
                        <small class="text-success d-none" id="chat-header-online"><i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Online</small>
                        <small class="text-muted" id="chat-header-offline">Offline</small>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="chat-messages" id="chat-messages">
                    {{-- Messages injected here --}}
                </div>

                {{-- Reply Preview --}}
                <div id="reply-preview" class="d-none px-3 py-2 bg-light border-top border-primary border-4 rounded-top-4 mx-3 animate__animated animate__fadeInUp animate__faster">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small">
                            <div class="fw-bold text-primary mb-0" id="reply-name">Balas ke...</div>
                            <div class="text-muted text-truncate" id="reply-text" style="max-width: 300px;">Pesan asli...</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-light rounded-circle" onclick="cancelReply()"><i class="bi bi-x"></i></button>
                    </div>
                </div>

                {{-- Input --}}
                <div class="chat-input-area">
                    <form id="chat-form" onsubmit="sendMessage(event)">
                            <div class="position-relative">
                                <button type="button" class="btn btn-light rounded-circle text-muted border-0" id="emoji-btn">
                                    <i class="bi bi-emoji-smile fs-5"></i>
                                </button>
                                <div id="emoji-picker" class="d-none position-absolute bottom-100 start-0 mb-2 bg-white shadow-lg rounded-3 p-2 border" style="width: 250px; z-index: 1000; height: 200px; overflow-y: auto;">
                                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                                        <!-- Emojis will be injected here -->
                                    </div>
                                </div>
                            </div>
                            <input type="text" id="chat-input" class="form-control border-0 bg-transparent shadow-none" placeholder="Ketik pesan..." autocomplete="off" required>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Kirim <i class="bi bi-send ms-1"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentChatUserId = null;
    const authId = {{ auth()->id() }};

    // Load Conversations on Init
    document.addEventListener('DOMContentLoaded', () => {
        loadConversations();
        initRealtimeEcho();
        initEmojiPicker();

        // Check if there is a 'user' parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        const targetUserId = urlParams.get('user');
        if (targetUserId) {
            // Fetch user info first to open chat
            fetch(`/api/users/${targetUserId}`)
                .then(r => r.json())
                .then(user => {
                    openChat(user.id, user.name, user.avatar, user.is_online);
                })
                .catch(err => console.error('Failed to pre-open chat:', err));
        }
    });

    function initEmojiPicker() {
        const emojis = ['😀','😃','😄','😁','😆','😅','😂','🤣','😊','😇','🙂','🙃','😉','😌','😍','🥰','😘','😗','😙','😚','😋','😛','😝','😜','🤪','🤨','🧐','🤓','😎','🤩','🥳','😏','😒','😞','😔','😟','😕','🙁','☹️','😣','😖','😫','😩','🥺','😢','😭','😤','😠','😡','🤬','🤯','😳','🥵','🥶','😱','😨','😰','😥','😓','🤗','🤔','🤭','🤫','🤥','😶','😐','😑','😬','🙄','😯','😦','😧','😮','😲','🥱','😴','🤤','😪','😵','🤐','🥴','🤢','🤮','🤧','😷','🤒','🤕','🤑','🤠','😈','👿','👹','👺','🤡','💩','👻','💀','☠️','👽','👾','🤖','🎃','😺','😸','😻','😼','😽','🙀','😿','😾','🤲','👐','🙌','👏','🤝','👍','👎','👊','✊','🤛','🤜','🤞','✌️','🤟','🤘','👌','🤌','🤏','👈','👉','👆','👇','✋','🤚','🖐','🖖','👋','🤙','💪','🦾','🖕','✍️','🙏','🦶','🦵','🦿','💄','💋','👄','🦷','👅','👂','🦻','👃','👣','👁','👀','🧠','🫀','🫁','🦴','👤','👥','🫂'];
        
        const picker = document.getElementById('emoji-picker');
        const list = picker.querySelector('div');
        const btn = document.getElementById('emoji-btn');
        const input = document.getElementById('chat-input');

        emojis.forEach(emoji => {
            const span = document.createElement('span');
            span.innerText = emoji;
            span.style.cursor = 'pointer';
            span.style.fontSize = '1.2rem';
            span.onclick = () => {
                input.value += emoji;
                picker.classList.add('d-none');
                input.focus();
            };
            list.appendChild(span);
        });

        btn.onclick = (e) => {
            e.stopPropagation();
            picker.classList.toggle('d-none');
        };

        document.onclick = (e) => {
            if (!picker.contains(e.target) && e.target !== btn) {
                picker.classList.add('d-none');
            }
        };
    }

    function loadConversations() {
        fetch('/api/chat/conversations')
            .then(r => r.json())
            .then(data => {
                const list = document.getElementById('conversations-list');
                list.innerHTML = '';
                
                if(data.length === 0) {
                    list.innerHTML = '<div class="p-4 text-center text-muted small">Belum ada obrolan.</div>';
                    return;
                }

                data.forEach(conv => {
                    const unreadBadge = conv.unread_count > 0 ? `<div class="unread-badge">${conv.unread_count}</div>` : '';
                    const messagePreview = conv.is_sender ? `Anda: ${conv.last_message}` : conv.last_message;
                    
                    const onlineIndicator = conv.is_online ? '<div class="bg-success rounded-circle position-absolute" style="width:12px; height:12px; border:2px solid #fff; bottom:0; right:0;"></div>' : '';
                    
                    list.innerHTML += `
                        <div class="conversation-item d-flex align-items-center gap-3 ${currentChatUserId == conv.id ? 'active' : ''}" onclick="openChat(${conv.id}, '${conv.name}', '${conv.avatar}', ${conv.is_online})">
                            <div class="position-relative">
                                <img src="${conv.avatar}" class="rounded-circle object-fit-cover" width="45" height="45">
                                ${onlineIndicator}
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold text-truncate">${conv.name}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">${conv.last_message_time}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted text-truncate d-block" style="max-width: 150px;">${messagePreview}</small>
                                    ${unreadBadge}
                                </div>
                            </div>
                        </div>
                    `;
                });
            });
    }

    function openChat(userId, name, avatar, isOnline = false) {
        currentChatUserId = userId;
        
        // UI State
        document.getElementById('empty-chat-state').classList.add('d-none');
        document.getElementById('active-chat-state').classList.remove('d-none');
        document.getElementById('active-chat-state').classList.add('d-flex');
        
        // Header
        document.getElementById('chat-header-name').innerText = name;
        document.getElementById('chat-header-avatar').src = avatar;

        if (isOnline) {
            document.getElementById('chat-header-online').classList.remove('d-none');
            document.getElementById('chat-header-offline').classList.add('d-none');
        } else {
            document.getElementById('chat-header-online').classList.add('d-none');
            document.getElementById('chat-header-offline').classList.remove('d-none');
        }
        
        // Load messages
        const msgContainer = document.getElementById('chat-messages');
        msgContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm"></div></div>';
        
        fetch(`/api/chat/messages/${userId}`)
            .then(r => r.json())
            .then(messages => {
                msgContainer.innerHTML = '';
                messages.forEach(msg => {
                    appendMessageUI(msg);
                });
                scrollToBottom();
                
                // Mark as read in backend
                fetch(`/api/chat/messages/${userId}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                
                // Refresh conversation list to clear badge
                loadConversations();
            });

        // Mobile responsive switch
        if(window.innerWidth < 768) {
            document.getElementById('conversations-wrapper').classList.add('d-none');
            document.getElementById('chat-window-wrapper').classList.remove('d-none');
        }
    }

    function appendMessageUI(msg) {
        const msgContainer = document.getElementById('chat-messages');
        const alignmentClass = msg.is_mine ? 'message-mine' : 'message-theirs';
        
        let parentHtml = '';
        if (msg.parent) {
            parentHtml = `
                <div class="small bg-black bg-opacity-10 p-2 rounded mb-2 border-start border-4 border-primary">
                    <div class="fw-bold small opacity-75">${msg.parent.sender_name}</div>
                    <div class="text-truncate" style="max-width: 200px;">${msg.parent.message}</div>
                </div>
            `;
        }

        let deleteOption = '';
        if (msg.is_mine) {
            deleteOption = `
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item small text-danger" href="#" onclick="deleteMessage(${msg.id})">
                    <i class="bi bi-trash me-2"></i> Hapus
                </a></li>
            `;
        }

        const html = `
            <div class="message-bubble ${alignmentClass} animate__animated animate__fadeInUp animate__faster group" id="msg-${msg.id}">
                ${parentHtml}
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="message-text flex-grow-1">${msg.message}</div>
                    <div class="dropdown opacity-0 group-hover-opacity-100 transition-opacity">
                        <button class="btn btn-link btn-sm p-0 text-white text-opacity-75 text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius: 12px; min-width: 120px;">
                            <li><a class="dropdown-item small" href="#" onclick="setReply(${msg.id}, '${msg.message}', '${msg.is_mine ? 'Anda' : 'Alumni'}')"><i class="bi bi-reply me-2"></i> Balas</a></li>
                            <li><a class="dropdown-item small" href="#" onclick="forwardMessage('${msg.message}')"><i class="bi bi-share me-2"></i> Forward</a></li>
                            ${deleteOption}
                        </ul>
                    </div>
                </div>
                <div class="message-time">${msg.time}</div>
            </div>
        `;
        msgContainer.insertAdjacentHTML('beforeend', html);
    }

    function scrollToBottom() {
        const msgContainer = document.getElementById('chat-messages');
        msgContainer.scrollTop = msgContainer.scrollHeight;
    }

    let replyToId = null;

    function setReply(id, text, name) {
        replyToId = id;
        document.getElementById('reply-name').innerText = 'Balas ke ' + name;
        document.getElementById('reply-text').innerText = text;
        document.getElementById('reply-preview').classList.remove('d-none');
        document.getElementById('chat-input').focus();
    }

    function cancelReply() {
        replyToId = null;
        document.getElementById('reply-preview').classList.add('d-none');
    }

    function deleteMessage(id) {
        if (!confirm('Hapus pesan ini?')) return;

        fetch(`/api/chat/messages/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`msg-${id}`).classList.add('animate__fadeOut');
                setTimeout(() => document.getElementById(`msg-${id}`).remove(), 500);
            }
        });
    }

    function forwardMessage(text) {
        // Forward is basically opening chat list to pick someone
        // For now, let's just copy to clipboard or alert
        alert('Fitur Forward: Teks disalin. Silakan pilih alumni lain dan tempel pesan.');
        navigator.clipboard.writeText(text);
    }

    function sendMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const text = input.value.trim();
        if(!text || !currentChatUserId) return;
        
        const parentId = replyToId;
        cancelReply(); // clear UI instantly
        input.value = ''; // clear instantly
        
        // Send to server
        fetch('/api/chat/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                receiver_id: currentChatUserId,
                message: text,
                parent_id: parentId
            })
        })
        .then(async r => {
            if (!r.ok) {
                const errorData = await r.json();
                throw new Error(errorData.message || 'Server Error');
            }
            return r.json();
        })
        .then(data => {
            // If it's a reply, we might want to refresh to see the context or just append
            // Append with parent info
            appendMessageUI(data.data);
            scrollToBottom();
            loadConversations(); 
        })
        .catch(err => {
            console.error('Send Message Error:', err);
            alert('Gagal mengirim pesan: ' + err.message);
        });
    }

    function closeChatMobile() {
        document.getElementById('conversations-wrapper').classList.remove('d-none');
        document.getElementById('chat-window-wrapper').classList.add('d-none');
        currentChatUserId = null;
    }

    // Realtime Reverb Integration
    function initRealtimeEcho() {
        if(window.Echo) {
            window.Echo.private(`chat.${authId}`)
                .listen('NewMessageEvent', (e) => {
                    // Jika obrolan dengan pengirim sedang dibuka
                    if(currentChatUserId == e.sender_id) {
                        const newMsg = {
                            id: e.id,
                            sender_id: e.sender_id,
                            message: e.message,
                            is_mine: false,
                            time: new Date(e.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
                            avatar: e.sender.avatar
                        };
                        appendMessageUI(newMsg);
                        scrollToBottom();
                        
                        // Tandai terbaca
                        fetch(`/api/chat/messages/${e.sender_id}/read`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                    } else {
                        // Jika chat lain, putar suara notifikasi kecil (opsional)
                        // const audio = new Audio('/sounds/pop.mp3'); audio.play();
                    }
                    
                    // Update daftar obrolan
                    loadConversations();
                });
        }
    }
</script>
@endpush

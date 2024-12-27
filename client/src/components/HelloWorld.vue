<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Login</h2>
                <form @submit.prevent="login">
                    <div>
                        <label for="email">Email:</label>
                        <input class="form-control" type="email" id="email" v-model="email" required/>
                    </div>
                    <div>
                        <label for="password">Password:</label>
                        <input class="form-control" type="password" id="password" v-model="password" required/>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <h1 v-if="user">Welcome {{user.name}}</h1>
                <h2>All Users</h2>
                <ul>
                    <li
                        style="background-color: antiquewhite;cursor: pointer"
                        v-for="user in allUsers"
                        :key="user.id"
                        @click="setReceiverId(user)"

                    >
                        {{ user.name }}
                    </li>
                </ul>
            </div>
            <div class="col-md-9">
                <div class="chat-wrapper">
                    <div class="chat-box">
                        <div class="chat-header">
                            <h3>Lets chat {{ receiver?.name }}</h3>
                        </div>

                        <div class="messages">
                            <div
                                class="message"
                                v-for="message in messages"
                                :key="message.id"
                                :class="{'sent': message.sender === userId, 'received': message.sender !== userId}">
                                <strong>{{ message.sender }}:</strong> {{ message.text }}
                            </div>
                        </div>

                        <div class="chat-input">
                            <input
                                v-model="newMessage"
                                placeholder="Type a message..."
                                type="text"
                            />
                            <button @click="sendMessage">Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import {ref, watch} from "vue";

const email = ref('');
const password = ref('');
const userId = ref(null);
const user = ref(null);
const token = ref(null);
const messages = ref([]);
const allUsers = ref([]);
const newMessage = ref('');
const receiverId = ref('');
const receiver = ref('');

watch(userId, (newUserId) => {
    if (newUserId) {
        window.Echo.channel(`chat-${newUserId}`).listen('MessageSent', (event) => {
            receiverId.value = event.data.sender_id
            messages.value.push({
                sender: event.data.sender_id,
                text: event.data.message,
            });
        });
    }
});

/*const triggerChat = () => {
    window.Echo.channel(`chat-${this.userId}`).listen('MessageSent', (event) => {
        this.messages.push({
            sender: event.data.sender,
            text: event.data.message,
        });
    });
}*/
const setReceiverId = (user) => {
    receiverId.value = user.id;
    receiver.value = user;
}
const getUsers = async () => {
    try {
        const response = await axios.post("http://localhost:8000/api/users", {}, {
            headers: {Authorization: `Bearer ${token.value}`}
        });

        if (response.data.success) {
            allUsers.value = response.data.users;
        } else {
           // error.value = response.data.message;
        }
    } catch (err) {
        this.error = "Login failed. Please check your credentials.";
    }
}
const login = async () => {
    try {
        const response = await axios.post("http://localhost:8000/api/login", {
            email: email.value,
            password: password.value,
        });

        if (response.data.success) {
            localStorage.setItem("auth_token", response.data.token);
            localStorage.setItem("user_id", response.data.user.id);
            userId.value = response.data.user.id;
            user.value = response.data.user;
            token.value = response.data.token;

            getUsers();
        } else {
            console.error(response.data.message);
        }
    } catch (err) {
        console.error("Login failed:", err);
    }
}

const sendMessage = async () => {
    if(!receiverId.value){
        alert('select user to chat')
        return
    }
    if (newMessage.value.trim() === '') return;

    try {
        const response = await axios.post('http://localhost:8000/api/sendSms',
            {
                receiver_id: receiverId.value,
                message: newMessage.value
            },
            {
                headers: {Authorization: `Bearer ${token.value}`}
            }
        );

        if (response.data.success) {
            messages.value.push({
                sender: userId.value,
                text: newMessage.value,
            });

            newMessage.value = '';

        } else {
            console.error('Failed to send message');
        }
    } catch (err) {
        // this.error = "Login failed.";
    }
}


/*watch: {
    userId(newId) {
        if (newId) {
            this.triggerChat();
        }
    },
},*/

</script>

<style scoped>
/* Wrapper to center the chat box on the page */
.chat-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Full viewport height */
    background-color: #f4f4f4; /* Optional background color */
}

.chat-box {
    display: flex;
    flex-direction: column;
    width: 350px; /* Set width to make it smaller */
    max-width: 100%; /* Make it responsive */
    height: 500px; /* Set height for the chat box */
    border: 1px solid #ccc;
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optional for a nice shadow effect */
}

.chat-header {
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    text-align: center;
}

.messages {
    flex-grow: 1;
    overflow-y: auto;
    padding: 10px;
    height: 100%;
}

.message {
    margin-bottom: 10px;
    padding: 8px;
    border-radius: 5px;
    background-color: #f1f1f1;
}

.message.sent {
    background-color: #cfe9ff;
    text-align: right;
}

.message.received {
    background-color: #e7f4e4;
    text-align: left;
}

.chat-input {
    display: flex;
    padding: 10px;
    border-top: 1px solid #ddd;
    background-color: #f9f9f9;
}

.chat-input input {
    flex-grow: 1;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ddd;
    margin-right: 10px;
}

.chat-input button {
    padding: 8px 16px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.chat-input button:hover {
    background-color: #45a049;
}
</style>

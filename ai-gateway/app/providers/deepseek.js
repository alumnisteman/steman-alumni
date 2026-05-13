const axios = require('axios');

module.exports = {
  name: 'deepseek',
  enabled: process.env.DEEPSEEK_API_KEY ? true : false,

  async ask(prompt) {
    try {
        const response = await axios.post(
          'https://api.deepseek.com/v1/chat/completions',
          {
            model: 'deepseek-chat',
            messages: [
              {
                role: 'user',
                content: prompt
              }
            ]
          },
          {
            headers: {
              Authorization: `Bearer ${process.env.DEEPSEEK_API_KEY}`
            },
            timeout: 30000
          }
        );

        return response.data.choices[0].message.content;
    } catch (e) {
        console.error('DeepSeek Provider Error:', e.message);
        return null;
    }
  }
};

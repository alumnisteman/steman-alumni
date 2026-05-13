const axios = require('axios');

module.exports = {
  name: 'openai',
  enabled: process.env.OPENAI_API_KEY ? true : false,

  async ask(prompt) {
    try {
        const response = await axios.post(
          'https://api.openai.com/v1/chat/completions',
          {
            model: 'gpt-4o-mini',
            messages: [
              {
                role: 'user',
                content: prompt
              }
            ]
          },
          {
            headers: {
              Authorization: `Bearer ${process.env.OPENAI_API_KEY}`
            },
            timeout: 30000
          }
        );

        return response.data.choices[0].message.content;
    } catch (e) {
        console.error('OpenAI Provider Error:', e.message);
        return null;
    }
  }
};

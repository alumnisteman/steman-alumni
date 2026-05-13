const axios = require('axios');

module.exports = {
  name: 'openrouter',
  enabled: process.env.OPENROUTER_API_KEY ? true : false,

  async ask(prompt) {
    try {
        const response = await axios.post(
          'https://openrouter.ai/api/v1/chat/completions',
          {
            model: process.env.OPENROUTER_MODEL || 'google/gemini-2.0-flash-exp:free',
            messages: [
              {
                role: 'user',
                content: prompt
              }
            ]
          },
          {
            headers: {
              Authorization: `Bearer ${process.env.OPENROUTER_API_KEY}`
            },
            timeout: 30000
          }
        );

        return response.data.choices[0].message.content;
    } catch (e) {
        console.error('OpenRouter Provider Error:', e.message);
        return null;
    }
  }
};

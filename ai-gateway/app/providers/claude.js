const axios = require('axios');

module.exports = {
  name: 'claude',
  enabled: process.env.CLAUDE_API_KEY ? true : false,

  async ask(prompt) {
    try {
        const response = await axios.post(
          'https://api.anthropic.com/v1/messages',
          {
            model: 'claude-3-5-sonnet-20241022',
            max_tokens: 1024,
            messages: [
              {
                role: 'user',
                content: prompt
              }
            ]
          },
          {
            headers: {
              'x-api-key': process.env.CLAUDE_API_KEY,
              'anthropic-version': '2023-06-01'
            },
            timeout: 30000
          }
        );

        return response.data.content[0].text;
    } catch (e) {
        console.error('Claude Provider Error:', e.message);
        return null;
    }
  }
};

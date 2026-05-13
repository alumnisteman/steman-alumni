const axios = require('axios');

module.exports = {
  name: 'ollama',
  enabled: process.env.OLLAMA_URL ? true : false,

  async ask(prompt) {
    try {
        const response = await axios.post(
          `${process.env.OLLAMA_URL}/api/generate`,
          {
            model: 'llama3',
            prompt,
            stream: false
          },
          {
            timeout: 60000
          }
        );

        return response.data.response;
    } catch (e) {
        console.error('Ollama Provider Error:', e.message);
        return null;
    }
  }
};

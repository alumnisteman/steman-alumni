const axios = require('axios');

module.exports = {
  name: 'gemini',
  enabled: process.env.GEMINI_API_KEY ? true : false,

  async ask(prompt) {
    try {
        const response = await axios.post(
          `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${process.env.GEMINI_API_KEY}`,
          {
            contents: [
              {
                parts: [
                  {
                    text: prompt
                  }
                ]
              }
            ]
          },
          {
            timeout: 30000
          }
        );

        return response.data.candidates[0].content.parts[0].text;
    } catch (e) {
        console.error('Gemini Provider Error:', e.message);
        return null;
    }
  }
};

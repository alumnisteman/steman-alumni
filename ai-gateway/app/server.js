require('dotenv').config();

const express = require('express');
const crypto = require('crypto');

const openai = require('./providers/openai');
const gemini = require('./providers/gemini');
const claude = require('./providers/claude');
const ollama = require('./providers/ollama');

const cache = require('./utils/cache');

const app = express();

app.use(express.json({ limit: '20mb' }));

const providers = [
  openai,
  gemini,
  claude,
  ollama
];

app.get('/health', async (req, res) => {
  res.json({
    status: 'ok',
    providers: providers.map(p => ({
      name: p.name,
      enabled: p.enabled
    }))
  });
});

app.post('/chat', async (req, res) => {
  const prompt = req.body.prompt;

  if (!prompt) {
    return res.status(400).json({
      error: 'prompt required'
    });
  }

  const hash = crypto
    .createHash('md5')
    .update(prompt)
    .digest('hex');

  const cached = await cache.get(hash);

  if (cached) {
    return res.json({
      cached: true,
      ...JSON.parse(cached)
    });
  }

  for (const provider of providers) {
    if (!provider.enabled) continue;

    try {
      console.log(`Trying ${provider.name}`);

      const response = await provider.ask(prompt);

      if (response) {
          const result = {
            provider: provider.name,
            response
          };

          await cache.set(hash, JSON.stringify(result));
          return res.json(result);
      }

    } catch (err) {
      console.error(`${provider.name} failed`, err.message);
    }
  }

  return res.status(500).json({
    error: 'all providers offline'
  });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`AI Gateway running on ${PORT}`);
});

const Redis = require('ioredis');

const redis = new Redis({
  host: process.env.REDIS_HOST || 'redis-ai',
  port: process.env.REDIS_PORT || 6379
});

module.exports = {
  async get(key) {
    try {
        return await redis.get(key);
    } catch (e) {
        return null;
    }
  },

  async set(key, value) {
    try {
        return await redis.set(key, value, 'EX', 300);
    } catch (e) {
        return null;
    }
  }
};

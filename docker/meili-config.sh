#!/bin/sh
curl -s -X PATCH "http://steman_meilisearch:7700/indexes/users/settings" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer stemanMasterKey123" \
  -d '{"sortableAttributes":["_geo"],"filterableAttributes":["major","graduation_year","id"]}'

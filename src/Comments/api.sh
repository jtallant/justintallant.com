curl -X POST http://localhost:4000/api/comments \
     -H "Content-Type: application/json" \
     -d '{"post_id": 1, "author": "Alice", "content": "This is a test comment", "entry_uri": "should-we-follow-srp-in-controllers"}'

curl -G "http://localhost:4000/api/comments" --data-urlencode "entry_uri=should-we-follow-srp-in-controllers"

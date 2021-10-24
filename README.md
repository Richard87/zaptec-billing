# Zaptec Billing

## Generate Zaptec Objects:
openapi-generator generate -g php --additional-properties=prependFormOrBodyParameters=true -o src/Domain/Zaptec -i https://api.zaptec.com/swagger/v1/swagger.json

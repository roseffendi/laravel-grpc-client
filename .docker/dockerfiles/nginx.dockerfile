#
# Build nginx image
#

# Define base image
FROM nginx:1.15.8-alpine

# Copy nginx configuration
COPY ./.docker/config/nginx/default.conf /etc/nginx/conf.d/default.conf
# Use an official Node.js runtime as a parent image
FROM node:14

# Create a directory for your application
WORKDIR /app

# Copy the package.json and package-lock.json files and install dependencies
COPY package*.json ./
RUN npm install

# Copy your Node.js application code into the container
COPY . .

# Expose port 3000
EXPOSE 3000

# Start your Node.js application
CMD ["node", "index.js"]

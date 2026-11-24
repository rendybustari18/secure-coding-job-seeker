<template>
  <footer class="bg-dark text-white text-center py-3 mt-5">
    <div class="container">
      <p>&copy; 2024 Job Portal. All rights reserved.</p>
      <p>
        <a href="#" class="text-white me-3">Privacy Policy</a>
        <a href="#" class="text-white me-3">Terms of Service</a>
        <a href="#" class="text-white">Contact Us</a>
      </p>
    </div>
  </footer>
</template>

<script>
window.makeRequest = function (url, data) {
  return fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  })
}

window.saveToken = function (token) {
  localStorage.setItem('jwt_token', token)
}

window.getToken = function () {
  return localStorage.getItem('jwt_token')
}
</script>


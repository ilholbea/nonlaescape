export function loadUserData(restUrl, nonce, debug) {
  return fetch(`${restUrl}shutterpress/v1/user-data`, {
    method: 'GET',
    credentials: 'include',
    headers: { 'X-WP-Nonce': nonce },
  })
    .then((res) => {
      if (!res.ok) throw new Error('Failed to fetch user data');
      return res.json();
    })
    .catch((err) => {
      if (debug) console.error('Fetch failed:', err);
      throw err;
    });
}

// --- Download Handler ---
export function bindDownloadHandler() {
  jQuery(document).on('click', '.sp-gallery-download-icon , .lg-download', function (e) {
    e.preventDefault();
    const $el = jQuery(this);
    const href = $el.attr('href');
    if (!href) {
      if (shutterpressData.debug) console.error('Missing download URL.');
      return;
    }
    const url = new URL(href, window.location.origin);
    const galleryId = url.searchParams.get('gallery_id');
    const imageId = url.searchParams.get('image_id');
    const nonce = url.searchParams.get('download_nonce');
    //  const token = url.searchParams.get('download_token');
    if (!galleryId || !imageId) {
      if (shutterpressData.debug) console.error('Missing download parameters.');
      return;
    }
    // Always send the global REST nonce when available, even for guests.
    const headers = {};
    if (typeof shutterpressData !== 'undefined' && shutterpressData.nonce) {
      headers['X-WP-Nonce'] = shutterpressData.nonce;
    }
    fetch(href, {
      method: 'GET',
      headers,
      credentials: 'same-origin',
    })
      .then((res) => {
        if (!res.ok) throw new Error('Download failed');
        const disposition = res.headers.get('Content-Disposition');
        let filename = `image-${imageId}.jpg`;
        if (disposition && disposition.includes('filename=')) {
          const match = disposition.match(/filename="?([^\";]+)"?/);
          if (match && match[1]) filename = match[1];
        }
        return res.blob().then((blob) => ({ blob, filename }));
      })
      .then(({ blob, filename }) => {
        const downloadUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = downloadUrl;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
      })
      .catch((err) => {
        if (shutterpressData.debug) console.error('Download error:', err);
      });
  });
}

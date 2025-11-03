document.addEventListener("DOMContentLoaded", () => {
  const modal = document.querySelector("#commentModal")
  const closeModal = document.querySelector("#closeModal")
  const openButtons = document.querySelectorAll(".open-comment-modal")
  const commentForm = document.querySelector("#commentform")

  if (!modal || !commentForm) return

  // --- Open modal and set post ID ---
  openButtons.forEach(button => {
    button.addEventListener("click", () => {
      const postId = button.dataset.postId
      modal.classList.remove("hidden")

      // Set hidden input for comment_post_ID
      let postIdInput = commentForm.querySelector('input[name="comment_post_ID"]')
      if (postIdInput) {
        postIdInput.value = postId
      } else {
        postIdInput = document.createElement("input")
        postIdInput.type = "hidden"
        postIdInput.name = "comment_post_ID"
        postIdInput.value = postId
        commentForm.appendChild(postIdInput)
      }
    })
  })

  // --- Close modal ---
  closeModal.addEventListener("click", () => {
    modal.classList.add("hidden")
    commentForm.reset()
  })

  // --- AJAX submit ---
  commentForm.addEventListener("submit", async e => {
    e.preventDefault()
    const formData = new FormData(commentForm)
    formData.append("action", "ajax_comment_submit")
    formData.append("nonce", ajax_comments_params.nonce)

    try {
      const response = await fetch(ajax_comments_params.ajax_url, {
        method: "POST",
        body: formData,
      })
      const data = await response.json()
      console.log("AJAX Response:", data)

      if (data.success) {
        // Insert comment in the correct list
        const commentsList =
          document.querySelector(".comment-list") ||
          document.querySelector(".comments-area ol") ||
          document.querySelector(".comments-area ul")

        let newCommentEl = null
        if (commentsList) {
          commentsList.insertAdjacentHTML("beforeend", data.comment_html)
          // Prefer selecting by returned ID, fallback to the last list item
          if (data.comment_id) {
            newCommentEl = document.getElementById(`comment-${data.comment_id}`)
          }
          if (!newCommentEl) {
            newCommentEl = commentsList.lastElementChild
          }
        }

        commentForm.reset()
        modal.classList.add("hidden")
        document.body.classList.remove("overflow-hidden")

        // Smooth-scroll to the new comment and update URL hash
        if (newCommentEl && newCommentEl.scrollIntoView) {
          newCommentEl.scrollIntoView({ behavior: "smooth", block: "start" })
          if (newCommentEl.id) {
            history.replaceState(null, "", `#${newCommentEl.id}`)
          }
        }
        
        if (data.success && data.comment_link) {
          window.location.href = data.comment_link
        }
        console.log("✅ Comment submitted successfully!")
      } else {
        console.log(`❌ Error: ${data.message}`)
      }
    } catch (error) {
      console.error("AJAX Error:", error)
      alert("❌ Something went wrong.")
    }
  })
})

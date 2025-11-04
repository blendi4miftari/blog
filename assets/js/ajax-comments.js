document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("commentModal")
  const closeBtn = document.getElementById("closeModal")
  const openButtons = document.querySelectorAll(".open-comment-modal")
  const commentForm = document.getElementById("commentform")
  const submitBtn = commentForm?.querySelector("#submit")

  if (!commentForm) return

  openButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      const postId = btn.dataset.postId
      const parentId = btn.dataset.parentId || "0"

      setHidden("comment_post_ID", postId)
      setHidden("comment_parent", parentId)

      modal?.classList.remove("hidden")
      document.body.classList.add("overflow-hidden")
    })
  })

  closeBtn?.addEventListener("click", () => {
    modal?.classList.add("hidden")
    document.body.classList.remove("overflow-hidden")
    commentForm.reset()
  })

  modal?.addEventListener("click", e => {
    if (e.target === modal) closeBtn?.click()
  })

  // Reply
  document.addEventListener("click", e => {
    const link = e.target.closest(".comment-reply-link")
    if (!link) return

    e.preventDefault()

    const postIdEl =
      link.closest("[data-post-id]") || document.querySelector("[data-post-id]")
    const postId = postIdEl?.dataset.postId

    const parentId =
      link.dataset.commentid || link.getAttribute("data-commentid")

    if (!postId || !parentId) {
      console.error("Missing post ID or parent ID for reply")
      return
    }

    setHidden("comment_post_ID", postId)
    setHidden("comment_parent", parentId)

    const title = document.getElementById("reply-title")
    if (title) {
      title.textContent = `Reply to ${link.dataset.replyto || "comment"}`
    }

    document
      .getElementById("commentform")
      ?.scrollIntoView({ behavior: "smooth" })
  })

  // Form submit
  commentForm.addEventListener("submit", async e => {
    e.preventDefault()

    const author = commentForm.querySelector("#author")
    const email = commentForm.querySelector("#email")
    const comment = commentForm.querySelector("#comment")

    let valid = true
    if (author && !validateText(author)) valid = false
    if (email && !validateEmail(email)) valid = false
    if (!validateText(comment)) valid = false
    if (!valid) return

    const fd = new FormData(commentForm)
    fd.append("action", "ajax_comment_submit")
    fd.append("nonce", ajax_comments_params.nonce)

    submitBtn.disabled = true
    submitBtn.value = "Submitting…"

    try {
      const res = await fetch(ajax_comments_params.ajax_url, {
        method: "POST",
        body: fd,
      })

      let data
      try {
        data = await res.json()
      } catch (err) {
        console.error("Invalid JSON:", await res.text())
        alert("Server error – see console.")
        return
      }

      if (data.success) {
        handleSuccess(data.data)
      } else {
        alert(data.data?.message || "Failed")
      }
    } catch (err) {
      console.error(err)
      alert("Network error")
    } finally {
      submitBtn.disabled = false
      submitBtn.value = "Post Comment"
    }
  })

  // Helpers
  function setHidden(name, value) {
    let input = commentForm.querySelector(`input[name="${name}"]`)
    if (!input) {
      input = document.createElement("input")
      input.type = "hidden"
      input.name = name
      commentForm.appendChild(input)
    }
    input.value = value
  }

  function validateText(el) {
    const ok = el.value.trim().length >= 2
    el.classList.toggle("error", !ok)
    return ok
  }
  function validateEmail(el) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    const ok = re.test(el.value.trim())
    el.classList.toggle("error", !ok)
    return ok
  }

  function handleSuccess({
    comment_html,
    comment_id,
    comment_count,
    is_approved,
  }) {
    const isArchive = !!document.getElementById("commentModal")

    if (isArchive) {
      commentForm.reset()
      modal.classList.add("hidden")
      document.body.classList.remove("overflow-hidden")

      const postId = commentForm.querySelector(
        'input[name="comment_post_ID"]'
      ).value

      document
        .querySelectorAll(`[data-post-id="${postId}"] .comment-count`)
        .forEach(countEl => {
          countEl.textContent =
            comment_count === 1 ? "1 Comment" : `${comment_count} Comments`
        })

      if (is_approved) {
        showToast("Comment posted!")
      } else {
        showToast("Waiting for moderator to approve")
      }
    } else {
      appendComment(comment_html, comment_id)
    }
  }

  function appendComment(html, id) {
    const parentId = commentForm.querySelector(
      'input[name="comment_parent"]'
    ).value
    let targetList = null

    if (parentId && parentId !== "0") {
      const parentLi = document.getElementById(`comment-${parentId}`)
      let children = parentLi?.querySelector(":scope > .children")
      if (!children) {
        children = document.createElement("ol")
        children.className = "children"
        parentLi?.appendChild(children)
      }
      targetList = children
    }

    if (!targetList) {
      targetList =
        document.querySelector(".comment-list") ||
        document.querySelector(".comments-area > ol") ||
        document.querySelector(".comments-area > ul")

      if (!targetList) {
        targetList = document.createElement("ol")
        targetList.className = "comment-list"
        document.querySelector(".comments-area")?.appendChild(targetList)
      }
    }

    targetList.insertAdjacentHTML("beforeend", html)
    const newEl = document.getElementById(`comment-${id}`)

    commentForm.reset()
    modal?.classList.add("hidden")
    document.body.classList.remove("overflow-hidden")

    if (newEl) {
      newEl.scrollIntoView({ behavior: "smooth", block: "center" })
      newEl.style.backgroundColor = "#fef3c7"
      setTimeout(() => (newEl.style.backgroundColor = ""), 3000)
      history.replaceState(null, "", `#comment-${id}`)
    }
  }

  function showToast(message) {
    const toast = document.createElement("div")
    toast.textContent = message
    Object.assign(toast.style, {
      position: "fixed",
      top: "50px",
      right: "20px",
      background: "#10b981",
      color: "#fff",
      padding: "12px 20px",
      borderRadius: "8px",
      zIndex: "9999",
      transform: "translateX(400px)",
      transition: "transform .3s ease",
      fontSize: "14px",
    })
    document.body.appendChild(toast)

    requestAnimationFrame(() => (toast.style.transform = "translateX(0)"))
    setTimeout(() => {
      toast.style.transform = "translateX(400px)"
      setTimeout(() => toast.remove(), 350)
    }, 2000)
  }
})

document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("commentModal")
  const closeBtn = document.getElementById("closeModal")
  const openButtons = document.querySelectorAll(".open-comment-modal")

  openButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      modal.classList.remove("hidden")
      document.body.classList.add("overflow-hidden")
    })
  })

  closeBtn.addEventListener("click", () => {
    modal.classList.add("hidden")
    document.body.classList.remove("overflow-hidden")
  })

  modal.addEventListener("click", e => {
    if (e.target === modal) {
      modal.classList.add("hidden")
      document.body.classList.remove("overflow-hidden")
    }
  })
})

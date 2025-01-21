import { Controller } from '@hotwired/stimulus';

/* form add and delete an ingredient */
export default class extends Controller {
  static values = {
    addLabel: String,
    deleteLabel: String
  }
  async connect() {
    this.index = this.element.childElementCount
    const btn = document.createElement('button')
    btn.setAttribute('class', 'btn btn-success')
    btn.innerText = this.addLabelValue || 'Ajouter un ingredient'
    btn.setAttribute('type', 'button')
    btn.addEventListener('click', this.addElement)
    this.element.childNodes.forEach(this.addDeleteElement)
    this.element.append(btn)      
  }
  addElement = (e) => {
    e.preventDefault()
    const element = document.createRange().createContextualFragment(
      this.element.dataset['prototype'].replaceAll('__name__', this.index)
    ).firstElementChild
    this.addDeleteElement(element)
    this.index++
    e.currentTarget.insertAdjacentElement('beforebegin', element)
  }
  addDeleteElement = (item) => {
    const deleteBtn = document.createElement('button')
    deleteBtn.setAttribute('class', 'btn btn-danger')
    deleteBtn.innerText = this.deleteLabelValue || 'Supprimer'
    deleteBtn.setAttribute('type', 'button')
    item.append(deleteBtn)
    deleteBtn.addEventListener('click', (e) => {
      e.preventDefault()
      item.remove()
    })
  }
}
/**
 * @typedef {Object} Contact
 * @property {string} id
 * @property {string} name
 * @property {string} email
 * @property {string} phone
 * @property {unknown} created
 */

/**
 * Managing the table through a class rather than directly
 */
export class ContactTable {
  /**
   *
   * @param {HTMLTableElement} element The table to modify
   * @param {Contact[]} [contacts=[]]
   */
  constructor(element, contacts = []) {
    /** @type {HTMLTableElement} */
    this.element = element;

    /** @type {Contact[]} */
    this.contacts = contacts;

    /** @type {(contact: Contact) => boolean} */
    this.filter = () => true;
  }

  /**
   * @param {Contact} contact
   */
  addContact(contact) {
    this.contacts.push(contact);
  }

  /**
   * @param {string} id
   */
  removeContact(id) {
    this.contacts.filter((contact) => contact.id !== id);
  }

  /**
   * Set the filter for when the table gets shown
   * @param {(contact: Contact) => boolean} filter
   */
  setFilter(filter) {
    this.filter = filter || (() => true);
  }

  /**
   * Update the table with the current contacts,
   * filtered by the function given to `setFilter`
   */
  display() {
    const rows = this.contacts
      .filter(this.filter)
      .map(this.makeContactRow, this);
    this.element.tBodies[0].replaceChildren(...rows);
  }

  /** @param {Contact} contact */
  makeBaseRow(contact) {
    // Make the row
    const row = document.createElement("tr");
    row.className = "table-row";
    row.dataset.editing = "false";

    // Normal fields
    for (const field of ["name", "email", "phone", "created"]) {
      const cell = row.insertCell();
      cell.className = "table-cell";

      const input = document.createElement("input");
      input.className = "table-input";
      input.type = "text";
      input.disabled = true;
      input.name = field;
      input.value = contact[field];

      cell.appendChild(input);
    }

    return row;
  }

  /** @param {Contact} contact */
  makeContactRow(contact) {
    const row = this.makeBaseRow(contact);

    // Option field
    const cell = row.insertCell();
    cell.className = "table-cell table-options";

    const edit = document.createElement("button");
    edit.innerText = "Edit";
    edit.className = "edit";

    // edit callback
    const editClick = () => {
      if (row.dataset.editing === "false") {
        // not selected, make it editable
        row.dataset.editing = "true";

        for (const cell of row.childNodes) {
          if (cell.childNodes[0].tagName === "INPUT") {
            cell.childNodes[0].disabled = false;
          } else {
            // The edit and delete button
            cell.childNodes[0].innerText = "Save";
          }
        }
      } else {
        // selected, make it not editable
        row.dataset.editing = "false";

        const newContact = { id: contact.id };

        for (const cell of row.childNodes) {
          if (cell.childNodes[0].tagName === "INPUT") {
            cell.childNodes[0].disabled = true;
            newContact[cell.childNodes[0].name] = cell.childNodes[0].value;
          } else {
            // The edit and delete button
            cell.childNodes[0].innerText = "Edit";
          }
        }

        // Send the edit
        console.log(newContact);

        // If it works, change it locally
        if (true) {
          this.removeContact(contact.id);
          this.addContact(newContact);
        }
      }
    };
    edit.onclick = editClick;

    const remove = document.createElement("button");
    remove.innerText = "Remove";
    remove.className = "remove";

    const removeClick = () => {
      this.removeContact(contact.id);
      // Hide the row. It won't be rendered, and once the table is displayed again,
      // the row won't be built at all
      row.style.display = "none";
    };
    remove.onclick = removeClick;

    cell.appendChild(edit);
    cell.appendChild(remove);

    return row;
  }

  /**
   * Add a row for making a new contact.
   * The row is directly added to the table
   */
  makeCreationRow() {
    const row = this.makeBaseRow({
      name: "",
      email: "",
      phone: "",
      created: "",
    });
    row.dataset.editing = true;

    // Make the inputs editable
    for (const cell of row.childNodes) {
      cell.childNodes[0].disabled = false;
    }

    const cell = row.insertCell();
    cell.className = "table-cell table-options";

    const create = document.createElement("button");
    create.innerText = "Create";
    create.className = "create";

    create.onclick = (event) => {
      const newContact = {};

      // Get all the values
      for (const cell of row.childNodes) {
        if (cell.childNodes[0].tagName === "INPUT") {
          newContact[cell.childNodes[0].name] = cell.childNodes[0].value;
        }
      }

      console.log(newContact); // POST-ing the data happens here
      this.addContact(newContact);
      this.display()
    };

    cell.appendChild(create);

    // Workaround to add a row that already exists
    this.element.tBodies[0].replaceChildren(
      ...this.element.tBodies[0].childNodes,
      row,
    );
  }

  /**
   * Remove all shown contacts from the table
   */
  clear() {
    this.element.tBodies[0].replaceChildren();
  }
}

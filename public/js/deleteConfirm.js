function sure(id) {
    if (confirm('Are you sure you want to delete this item?')) {
        location.href = "http://localhost:8000/delete?id=" + id;

    } else {
        // Do nothing!
    }
}

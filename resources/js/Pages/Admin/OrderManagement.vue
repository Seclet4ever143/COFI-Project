<template>
    <div :style="styles.container">
      <header :style="styles.header">
        <div :style="styles.headerContent">
          <h1 :style="styles.headerTitle">Luxury Order Management</h1>
        </div>
      </header>
  
      <main :style="styles.main">
        <div :style="styles.cardContainer">
          <div v-for="(card, index) in dashboardCards" :key="index" :style="styles.card">
            <div :style="styles.cardIcon">
              <span v-html="card.icon"></span>
            </div>
            <div :style="styles.cardContent">
              <div :style="styles.cardTitle">{{ card.title }}</div>
              <div :style="styles.cardValue">{{ card.value }}</div>
            </div>
          </div>
        </div>
  
        <div :style="styles.tableContainer">
          <div :style="styles.tableHeader">
            <h2 :style="styles.sectionTitle">Recent Orders</h2>
            <div :style="styles.searchContainer">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search orders..."
                :style="styles.searchInput"
              />
            </div>
          </div>
  
          <table :style="styles.table">
            <thead>
              <tr>
                <th v-for="header in tableHeaders" :key="header" 
                    :style="styles.tableHeaderCell"
                    @click="sortTable(header)">
                  {{ header }}
                  <span v-if="sortColumn === header.toLowerCase()">
                    {{ sortDirection === 'asc' ? '▲' : '▼' }}
                  </span>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in paginatedOrders" :key="order.id" :style="styles.tableRow">
                <td :style="styles.tableCell">{{ order.id }}</td>
                <td :style="styles.tableCell">{{ order.customer_name }}</td>
                <td :style="styles.tableCell">{{ formatCurrency(order.total_amount) }}</td>
                <td :style="styles.tableCell">
                  <span :style="getStatusStyle(order.status)">
                    {{ order.status }}
                  </span>
                </td>
                <td :style="styles.tableCell">{{ formatDate(order.created_at) }}</td>
                <td :style="styles.tableCell">
                  <button @click="openModal('view', order)" :style="styles.actionButton">View</button>
                  <button @click="openModal('edit', order)" :style="styles.actionButton">Edit</button>
                  <button @click="openModal('delete', order)" :style="styles.actionButton">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
  
          <div :style="styles.pagination">
            <div>
              <p :style="styles.paginationText">
                Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredOrders.length) }} of {{ filteredOrders.length }} results
              </p>
            </div>
            <div :style="styles.paginationButtons">
              <button
                v-for="page in totalPages"
                :key="page"
                @click="currentPage = page"
                :style="currentPage === page ? styles.activePageButton : styles.pageButton"
              >
                {{ page }}
              </button>
            </div>
          </div>
        </div>
      </main>
  
      <div v-if="showModal" :style="styles.modalOverlay">
        <div :style="styles.modalContent">
          <h3 :style="styles.modalTitle">{{ modalTitle }}</h3>
          <div v-if="modalType === 'view'">
            <p><strong>Order ID:</strong> {{ selectedOrder.id }}</p>
            <p><strong>Customer:</strong> {{ selectedOrder.customer_name }}</p>
            <p><strong>Total Amount:</strong> {{ formatCurrency(selectedOrder.total_amount) }}</p>
            <p><strong>Status:</strong> {{ selectedOrder.status }}</p>
            <p><strong>Date:</strong> {{ formatDate(selectedOrder.created_at) }}</p>
          </div>
          <div v-else-if="modalType === 'edit'">
            <label :style="styles.label">
              <span>Status</span>
              <select
                v-model="selectedOrder.status"
                :style="styles.select"
              >
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="completed">Completed</option>
              </select>
            </label>
          </div>
          <div v-else-if="modalType === 'delete'">
            <p :style="styles.modalText">Are you sure you want to delete this order? This action cannot be undone.</p>
          </div>
          <div :style="styles.modalActions">
            <button v-if="modalType === 'edit'" @click="updateOrder" :style="styles.primaryButton">
              Save Changes
            </button>
            <button v-if="modalType === 'delete'" @click="deleteOrder" :style="styles.dangerButton">
              Delete
            </button>
            <button @click="closeModal" :style="styles.secondaryButton">
              {{ modalType === 'view' ? 'Close' : 'Cancel' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        orders: [
          { id: 1, customer_name: 'John Doe', total_amount: 5000000, status: 'completed', created_at: '2023-05-15T10:00:00Z' },
          { id: 2, customer_name: 'Jane Smith', total_amount: 7500000, status: 'processing', created_at: '2023-05-14T14:30:00Z' },
          { id: 3, customer_name: 'Robert Johnson', total_amount: 10000000, status: 'pending', created_at: '2023-05-13T09:15:00Z' },
        ],
        searchQuery: '',
        currentPage: 1,
        itemsPerPage: 10,
        sortColumn: 'created_at',
        sortDirection: 'desc',
        showModal: false,
        modalType: '',
        selectedOrder: null,
        tableHeaders: ['Order ID', 'Customer', 'Total', 'Status', 'Date', 'Actions'],
        styles: {
          container: {
            minHeight: '100vh',
            background: 'linear-gradient(to bottom right, #111827, #000000)',
            color: '#ffffff',
            fontFamily: 'Arial, sans-serif',
          },
          header: {
            background: 'rgba(0, 0, 0, 0.5)',
            backdropFilter: 'blur(10px)',
            borderBottom: '1px solid #374151',
          },
          headerContent: {
            maxWidth: '1200px',
            margin: '0 auto',
            padding: '1.5rem 1rem',
          },
          headerTitle: {
            fontSize: '1.875rem',
            fontWeight: 'bold',
            background: 'linear-gradient(to right, #c084fc, #db2777)',
            WebkitBackgroundClip: 'text',
            WebkitTextFillColor: 'transparent',
          },
          main: {
            maxWidth: '1200px',
            margin: '0 auto',
            padding: '2rem 1rem',
          },
          cardContainer: {
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))',
            gap: '1.5rem',
            marginBottom: '2rem',
          },
          card: {
            background: '#1f2937',
            borderRadius: '0.5rem',
            padding: '1.5rem',
            display: 'flex',
            alignItems: 'center',
          },
          cardIcon: {
            background: '#7c3aed',
            borderRadius: '0.375rem',
            padding: '0.75rem',
            marginRight: '1rem',
          },
          cardContent: {
            flex: '1',
          },
          cardTitle: {
            fontSize: '0.875rem',
            fontWeight: 'medium',
            color: '#9ca3af',
            marginBottom: '0.25rem',
          },
          cardValue: {
            fontSize: '1.5rem',
            fontWeight: 'bold',
          },
          chartContainer: {
            background: '#1f2937',
            borderRadius: '0.5rem',
            padding: '1.5rem',
            marginBottom: '2rem',
          },
          sectionTitle: {
            fontSize: '1.5rem',
            fontWeight: 'bold',
            marginBottom: '1rem',
          },
          chart: {
            height: '16rem',
            background: '#374151',
            borderRadius: '0.375rem',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
          },
          chartPlaceholder: {
            color: '#9ca3af',
          },
          tableContainer: {
            background: '#1f2937',
            borderRadius: '0.5rem',
            padding: '1.5rem',
          },
          tableHeader: {
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            marginBottom: '1.5rem',
          },
          searchContainer: {
            position: 'relative',
          },
          searchInput: {
            background: '#374151',
            color: '#ffffff',
            borderRadius: '9999px',
            padding: '0.5rem 1rem',
            paddingRight: '2.5rem',
            border: 'none',
            outline: 'none',
          },
          table: {
            width: '100%',
            borderCollapse: 'separate',
            borderSpacing: '0 0.5rem',
          },
          tableHeaderCell: {
            textAlign: 'left',
            padding: '0.75rem 1rem',
            fontSize: '0.75rem',
            fontWeight: 'medium',
            textTransform: 'uppercase',
            color: '#9ca3af',
            cursor: 'pointer',
          },
          tableRow: {
            background: '#374151',
            transition: 'background-color 0.2s',
          },
          tableCell: {
            padding: '1rem',
            whiteSpace: 'nowrap',
          },
          actionButton: {
            background: 'none',
            border: 'none',
            color: '#9ca3af',
            cursor: 'pointer',
            marginRight: '0.5rem',
            transition: 'color 0.2s',
          },
          pagination: {
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            marginTop: '1.5rem',
          },
          paginationText: {
            fontSize: '0.875rem',
            color: '#9ca3af',
          },
          paginationButtons: {
            display: 'flex',
            gap: '0.5rem',
          },
          pageButton: {
            background: '#374151',
            color: '#9ca3af',
            border: 'none',
            borderRadius: '0.375rem',
            padding: '0.5rem 0.75rem',
            fontSize: '0.875rem',
            fontWeight: 'medium',
            cursor: 'pointer',
            transition: 'background-color 0.2s',
          },
          activePageButton: {
            background: '#7c3aed',
            color: '#ffffff',
            border: 'none',
            borderRadius: '0.375rem',
            padding: '0.5rem 0.75rem',
            fontSize: '0.875rem',
            fontWeight: 'medium',
            cursor: 'pointer',
          },
          modalOverlay: {
            position: 'fixed',
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            background: 'rgba(0, 0, 0, 0.5)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
          },
          modalContent: {
            background: '#1f2937',
            borderRadius: '0.5rem',
            padding: '1.5rem',
            maxWidth: '32rem',
            width: '100%',
          },
          modalTitle: {
            fontSize: '1.25rem',
            fontWeight: 'bold',
            marginBottom: '1rem',
          },
          modalText: {
            marginBottom: '1rem',
          },
          modalActions: {
            display: 'flex',
            justifyContent: 'flex-end',
            gap: '0.5rem',
            marginTop: '1.5rem',
          },
          label: {
            display: 'block',
            marginBottom: '0.5rem',
          },
          select: {
            width: '100%',
            padding: '0.5rem',
            borderRadius: '0.375rem',
            background: '#374151',
            color: '#ffffff',
            border: '1px solid #4b5563',
            marginTop: '0.25rem',
          },
          primaryButton: {
            background: '#7c3aed',
            color: '#ffffff',
            border: 'none',
            borderRadius: '0.375rem',
            padding: '0.5rem 1rem',
            fontSize: '0.875rem',
            fontWeight: 'medium',
            cursor: 'pointer',
            transition: 'background-color 0.2s',
          },
          secondaryButton: {
            background: '#4b5563',
            color: '#ffffff',
            border: 'none',
            borderRadius: '0.375rem',
            padding: '0.5rem 1rem',
            fontSize: '0.875rem',
            fontWeight: 'medium',
            cursor: 'pointer',
            transition: 'background-color 0.2s',
          },
          dangerButton: {
            background: '#dc2626',
            color: '#ffffff',
            border: 'none',
            borderRadius: '0.375rem',
            padding: '0.5rem 1rem',
            fontSize: '0.875rem',
            fontWeight: 'medium',
            cursor: 'pointer',
            transition: 'background-color 0.2s',
          },
        },
      };
    },
    computed: {
      filteredOrders() {
        return this.orders.filter(order =>
          order.customer_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          order.id.toString().includes(this.searchQuery)
        );
      },
      sortedOrders() {
        return [...this.filteredOrders].sort((a, b) => {
          const modifier = this.sortDirection === 'asc' ? 1 : -1;
          if (typeof a[this.sortColumn] === 'string') {
            return a[this.sortColumn].localeCompare(b[this.sortColumn]) * modifier;
          }
          return (a[this.sortColumn] - b[this.sortColumn]) * modifier;
        });
      },
      paginatedOrders() {
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        return this.sortedOrders.slice(startIndex, startIndex + this.itemsPerPage);
      },
      totalPages() {
        return Math.ceil(this.sortedOrders.length / this.itemsPerPage);
      },
      totalRevenue() {
        return this.filteredOrders.reduce((sum, order) => sum + order.total_amount, 0);
      },
      averageOrderValue() {
        const totalOrders = this.filteredOrders.length;
        return totalOrders ? this.totalRevenue / totalOrders : 0;
      },
      dashboardCards() {
        return [
          { title: 'Total Orders', value: this.filteredOrders.length, icon: '📦' },
          { title: 'Total Revenue', value: this.formatCurrency(this.totalRevenue), icon: '💰' },
          { title: 'Average Order Value', value: this.formatCurrency(this.averageOrderValue), icon: '📊' },
        ];
      },
      modalTitle() {
        switch (this.modalType) {
          case 'view': return 'Order Details';
          case 'edit': return 'Edit Order';
          case 'delete': return 'Delete Order';
          default: return '';
        }
      },
    },
    methods: {
      formatCurrency(value) {
        return new Intl.NumberFormat('en-US', {
          style: 'currency',
          currency: 'USD',
          minimumFractionDigits: 0,
          maximumFractionDigits: 0,
        }).format(value);
      },
      formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        return new Date(dateString).toLocaleDateString('en-US', options);
      },
      sortTable(column) {
        if (this.sortColumn === column.toLowerCase()) {
          this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
          this.sortColumn = column.toLowerCase();
          this.sortDirection = 'asc';
        }
      },
      openModal(type, order) {
        this.modalType = type;
        this.selectedOrder = { ...order };
        this.showModal = true;
      },
      closeModal() {
        this.showModal = false;
        this.selectedOrder = null;
      },
      updateOrder() {
        const index = this.orders.findIndex(o => o.id === this.selectedOrder.id);
        if (index !== -1) {
          this.orders[index] = { ...this.selectedOrder };
        }
        this.closeModal();
      },
      deleteOrder() {
        this.orders = this.orders.filter(o => o.id !== this.selectedOrder.id);
        this.closeModal();
      },
      getStatusStyle(status) {
        let backgroundColor, color;
        switch (status.toLowerCase()) {
          case 'completed':
            backgroundColor = '#10B981';
            color = '#ECFDF5';
            break;
          case 'processing':
            backgroundColor = '#3B82F6';
            color = '#EFF6FF';
            break;
          case 'pending':
            backgroundColor = '#F59E0B';
            color = '#FFFBEB';
            break;
          default:
            backgroundColor = '#6B7280';
            color = '#F3F4F6';
        }
        return {
          backgroundColor,
          color,
          padding: '0.25rem 0.5rem',
          borderRadius: '9999px',
          fontSize: '0.75rem',
          fontWeight: 'medium',
        };
      },
    },
  };
  </script>
  
  
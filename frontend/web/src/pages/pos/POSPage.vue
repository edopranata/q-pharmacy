<template>
  <q-page class="q-pa-md">
    <div class="row q-gutter-md">
      <!-- Product Selection -->
      <div class="col-12 col-md-7">
        <q-card>
          <q-card-section>
            <div class="text-h6 q-mb-md">Products</div>
            <q-input
              v-model="searchProduct"
              placeholder="Search products..."
              outlined
              dense
              class="q-mb-md"
            >
              <template v-slot:prepend>
                <q-icon name="search" />
              </template>
            </q-input>
            
            <div class="row q-gutter-sm">
              <div
                v-for="product in filteredProducts"
                :key="product.id"
                class="col-6 col-sm-4 col-md-3"
              >
                <q-card
                  class="cursor-pointer product-card"
                  @click="addToCart(product)"
                >
                  <q-card-section class="text-center">
                    <div class="text-subtitle2">{{ product.name }}</div>
                    <div class="text-caption text-grey-6">{{ product.category?.name }}</div>
                    <div class="text-h6 text-primary q-mt-sm">
                      {{ formatCurrency(product.selling_price) }}
                    </div>
                    <div class="text-caption">
                      Stock: {{ product.stock }}
                    </div>
                  </q-card-section>
                </q-card>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Cart -->
      <div class="col-12 col-md-5">
        <q-card>
          <q-card-section>
            <div class="text-h6 q-mb-md">Cart</div>
            
            <!-- Customer Info -->
            <q-input
              v-model="customerName"
              label="Customer Name (Optional)"
              outlined
              dense
              class="q-mb-md"
            />

            <!-- Cart Items -->
            <div v-if="cartItems.length === 0" class="text-center text-grey-6 q-py-lg">
              No items in cart
            </div>
            
            <div v-else>
              <div
                v-for="item in cartItems"
                :key="item.id"
                class="row items-center q-mb-sm"
              >
                <div class="col">
                  <div class="text-subtitle2">{{ item.name }}</div>
                  <div class="text-caption text-grey-6">
                    {{ formatCurrency(item.selling_price) }} x {{ item.quantity }}
                  </div>
                </div>
                <div class="col-auto">
                  <q-btn
                    flat
                    round
                    size="sm"
                    icon="remove"
                    @click="decreaseQuantity(item)"
                  />
                  <span class="q-mx-sm">{{ item.quantity }}</span>
                  <q-btn
                    flat
                    round
                    size="sm"
                    icon="add"
                    @click="increaseQuantity(item)"
                  />
                  <q-btn
                    flat
                    round
                    size="sm"
                    icon="delete"
                    color="negative"
                    @click="removeFromCart(item)"
                  />
                </div>
              </div>
              
              <q-separator class="q-my-md" />
              
              <!-- Totals -->
              <div class="row justify-between q-mb-sm">
                <span>Subtotal:</span>
                <span>{{ formatCurrency(subtotal) }}</span>
              </div>
              <div class="row justify-between q-mb-sm">
                <span>Tax (10%):</span>
                <span>{{ formatCurrency(tax) }}</span>
              </div>
              <div class="row justify-between text-h6 text-weight-bold">
                <span>Total:</span>
                <span>{{ formatCurrency(total) }}</span>
              </div>
              
              <q-separator class="q-my-md" />
              
              <!-- Payment -->
              <q-input
                v-model.number="paymentAmount"
                label="Payment Amount"
                type="number"
                outlined
                dense
                class="q-mb-md"
              />
              
              <div v-if="paymentAmount >= total" class="text-positive q-mb-md">
                Change: {{ formatCurrency(paymentAmount - total) }}
              </div>
              
              <div class="row q-gutter-sm">
                <q-btn
                  color="negative"
                  icon="clear"
                  label="Clear Cart"
                  @click="clearCart"
                  class="col"
                />
                <q-btn
                  color="primary"
                  icon="payment"
                  label="Process Payment"
                  :disable="cartItems.length === 0 || paymentAmount < total"
                  @click="processPayment"
                  class="col"
                />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useProductStore } from 'src/stores/product'
import { useTransactionStore } from 'src/stores/transaction'

export default {
  name: 'POSPage',
  setup() {
    const $q = useQuasar()
    const productStore = useProductStore()
    const transactionStore = useTransactionStore()
    const cartItems = ref([])
    const searchProduct = ref('')
    const customerName = ref('')
    const paymentAmount = ref(0)

    const products = computed(() => productStore.products)
    
    const filteredProducts = computed(() => {
      if (!searchProduct.value) return products.value
      return products.value.filter(product =>
        product.name.toLowerCase().includes(searchProduct.value.toLowerCase())
      )
    })

    const subtotal = computed(() => {
      return cartItems.value.reduce((sum, item) => {
        return sum + (item.selling_price * item.quantity)
      }, 0)
    })

    const tax = computed(() => {
      return subtotal.value * 0.1
    })

    const total = computed(() => {
      return subtotal.value + tax.value
    })

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(amount || 0)
    }

    const fetchProducts = async () => {
      try {
        await productStore.fetchProducts()
      } catch (error) {
        console.error('Error fetching products:', error.message)
      }
    }

    const addToCart = (product) => {
      if (product.stock <= 0) {
        $q.notify({
          type: 'negative',
          message: 'Product out of stock'
        })
        return
      }

      const existingItem = cartItems.value.find(item => item.id === product.id)
      if (existingItem) {
        if (existingItem.quantity < product.stock) {
          existingItem.quantity++
        } else {
          $q.notify({
            type: 'warning',
            message: 'Not enough stock'
          })
        }
      } else {
        cartItems.value.push({
          ...product,
          quantity: 1
        })
      }
    }

    const increaseQuantity = (item) => {
      const product = products.value.find(p => p.id === item.id)
      if (item.quantity < product.stock) {
        item.quantity++
      } else {
        $q.notify({
          type: 'warning',
          message: 'Not enough stock'
        })
      }
    }

    const decreaseQuantity = (item) => {
      if (item.quantity > 1) {
        item.quantity--
      } else {
        removeFromCart(item)
      }
    }

    const removeFromCart = (item) => {
      const index = cartItems.value.findIndex(cartItem => cartItem.id === item.id)
      if (index > -1) {
        cartItems.value.splice(index, 1)
      }
    }

    const clearCart = () => {
      $q.dialog({
        title: 'Confirm',
        message: 'Are you sure you want to clear the cart?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        cartItems.value = []
        paymentAmount.value = 0
      })
    }

    const processPayment = async () => {
      if (paymentAmount.value < total.value) {
        $q.notify({
          type: 'negative',
          message: 'Insufficient payment amount'
        })
        return
      }

      try {
        const transactionData = {
          customer_name: customerName.value || 'Walk-in Customer',
          items: cartItems.value.map(item => ({
            product_id: item.id,
            quantity: item.quantity,
            price: item.selling_price
          })),
          subtotal: subtotal.value,
          tax: tax.value,
          total: total.value,
          payment_amount: paymentAmount.value,
          change: paymentAmount.value - total.value
        }

        await transactionStore.createTransaction(transactionData)
        
        $q.notify({
          type: 'positive',
          message: 'Transaction completed successfully'
        })

        // Reset form
        cartItems.value = []
        customerName.value = ''
        paymentAmount.value = 0
        
        // Refresh products to update stock
        fetchProducts()
      } catch (error) {
        $q.notify({
          type: 'negative',
          message: error.response?.data?.message || 'Failed to process transaction'
        })
      }
    }

    onMounted(() => {
      fetchProducts()
    })

    return {
      products,
      cartItems,
      searchProduct,
      customerName,
      paymentAmount,
      filteredProducts,
      subtotal,
      tax,
      total,
      formatCurrency,
      addToCart,
      increaseQuantity,
      decreaseQuantity,
      removeFromCart,
      clearCart,
      processPayment
    }
  }
}
</script>
<template>
    <div class="widget">
        <h5>Total Brands</h5>
        <p>{{ brandCount }}</p>
    </div>
</template>

<script>
export default {
    data() {
        return {
            brandCount: 0,
        };
    },
    mounted() {
        axios.get('/api/brands/count')
            .then(response => {
                this.brandCount = response.data.count;
            })
            .catch(error => console.error(error));
    }
};
</script>

<style scoped>
.widget {
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 8px;
}
</style>

<template>
  <div>
    <q-chip :color="color" clickable text-color="white" @click="emitirEvento()">
      {{ text }}
      <q-tooltip
        :anchor="tooltipAnchor"
        :self="tooltipSelf"
        :offset="tooltipOffset"
        transition-show="flip-right"
        transition-hide="flip-left"
      >
        <span class="text-subtitle2">{{ tooltipText }}</span>
      </q-tooltip>
    </q-chip>
  </div>
</template>

<script>
export default {
  name: "InfoCard",
  props: {
    text: {
      type: String,
      default: "",
    },
    tooltipText: {
      type: String,
      default: "",
    },
    tooltipAnchor: {
      type: String,
      default: "",
    },
    tooltipSelf: {
      type: String,
      default: "",
    },
    tooltipOffset: {
      type: String,
      default: "",
    },
    color: {
      type: String,
      default: "primary",
    },
  },
  data() {
    return {
      emitirEvento() {
        this.$emit("click");
      },
    };
  },
};
</script>

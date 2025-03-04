"use strict";

(function ($) {
  $(document).ready(function () {
    $('body').addClass('stm_lms_chat_page');
    new Vue({
      el: '#stm_lms_chat',
      data: function data() {
        return {
          conversations: [],
          conversation: '',
          myMessage: '',
          myResponse: '',
          loading: false,
          updating: false,
          instructorPublic: chat_data.instructor_public,
          studentPublic: chat_data.student_public
        };
      },
      mounted: function mounted() {
        this.getConversation(false);
      },
      methods: {
        updateConversation: function updateConversation() {
          var vm = this;
          vm.updating = true;
          vm.getMessages(vm.conversations[vm.conversation]['conversation_info']['conversation_id'], true);
        },
        getConversation: function getConversation(update) {
          var vm = this;
          var url = stm_lms_ajaxurl + '?action=stm_lms_get_user_conversations&nonce=' + stm_lms_nonces['stm_lms_get_user_conversations'];
          this.$http.get(url).then(function (response) {
            response['body'].forEach(function (value, index) {
              vm.conversations.push(value);
            });
            if (vm.conversations.length) {
              vm.conversation = 0;
            }
          });
        },
        getMessages: function getMessages(conversation_id, update, just_send) {
          var vm = this;
          var url = stm_lms_ajaxurl + '?action=stm_lms_get_user_messages&nonce=' + stm_lms_nonces['stm_lms_get_user_messages'] + '&id=' + conversation_id + '&just_send=' + just_send;
          if (typeof vm.conversations[vm.conversation]['messages'] !== 'undefined' && !update) {
            vm.scrollMessagesBottom();
            return false;
          }
          if (!vm.conversations[vm.conversation].length) {
            this.$http.get(url).then(function (response) {
              vm.$set(vm.conversations[vm.conversation], 'messages', response.body['messages']);
              vm.scrollMessagesBottom();
            });
          }
        },
        changeChat: function changeChat(index) {
          this.conversation = index;
          var vm = this;
          var messageKeys = ["uf_new_messages", "ut_new_messages"];
          messageKeys.forEach(function (key) {
            if (vm.conversations[index]['conversation_info'][key] > 0) {
              vm.conversations[index]['conversation_info'][key] = 0;
            }
          });
          var url = stm_lms_ajaxurl + '?action=stm_lms_clear_new_messages&nonce=' + stm_lms_nonces['stm_lms_clear_new_messages'] + '&conversation_id=' + vm.conversations[vm.conversation]['conversation_info']['conversation_id'];
          vm.$http.get(url).then(function (response) {})["catch"](function (error) {
            vm.scrollMessagesBottom();
          });
        },
        scrollMessagesBottom: function scrollMessagesBottom() {
          var _this = this;
          this.updating = false;
          this.$nextTick(function () {
            var container = _this.$el.querySelector("#stm_lms_chat_messages");
            container.scrollTop = container.scrollHeight;
          });
        },
        sendMessage: function sendMessage() {
          var vm = this;
          vm.loading = true;
          var user_to = vm.conversations[vm.conversation]['companion']['id'];
          if (vm.myMessage) {
            var data = {
              to: user_to,
              message: vm.myMessage
            };
            var endpoint = stm_lms_ajaxurl + '?action=stm_lms_send_message&nonce=' + stm_lms_nonces['stm_lms_send_message'];
            vm.loading = true;
            this.$http.post(endpoint, data).then(function (response) {
              vm.getMessages(vm.conversations[vm.conversation]['conversation_info']['conversation_id'], true, true);
              if (response.body.response && 'error' === response.body.status) {
                vm['myResponse'] = response.body.response;
              }
              vm['myMessage'] = '';
              vm.loading = false;
            });
          }
        }
      },
      watch: {
        conversation: function conversation(conversation_id) {
          conversation_id = this.conversations[conversation_id]['conversation_info']['conversation_id'];
          this.getMessages(conversation_id, false, true);
        }
      }
    });
  });
})(jQuery);
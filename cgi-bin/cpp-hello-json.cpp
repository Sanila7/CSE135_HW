#include <iostream>
#include <cstdlib>
#include <ctime>

int main() {
    // Required CGI header
    std::cout << "Content-Type: application/json\n\n";

    // Get client IP
    const char* ip = std::getenv("REMOTE_ADDR");
    if (!ip) ip = "unknown";

    // Get current time
    std::time_t now = std::time(nullptr);
    char timebuf[100];
    std::strftime(timebuf, sizeof(timebuf), "%Y-%m-%d %H:%M:%S", std::localtime(&now));

    // Output JSON
    std::cout << "{\n";
    std::cout << "  \"greeting\": \"Hello World\",\n";
    std::cout << "  \"language\": \"C++\",\n";
    std::cout << "  \"generated_at\": \"" << timebuf << "\",\n";
    std::cout << "  \"client_ip\": \"" << ip << "\"\n";
    std::cout << "}\n";

    return 0;
}
